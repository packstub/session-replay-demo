// Makes real recordings to watch: customers use the shop in a headless browser while the recorder
// runs, exactly as it would for a person. Olivia's visit is the interesting one: her card fails at
// the provider, the front end swallows the error, she clicks Pay again and again, then reports it.
//
//   php artisan migrate:fresh --seed
//   node scripts/record-sessions.mjs            (APP_URL from .env, or BASE_URL=...)
//   HEADED=1 node scripts/record-sessions.mjs   (watch them)
import { chromium } from 'playwright'
import { readFileSync } from 'node:fs'

const env = readFileSync(new URL('../.env', import.meta.url), 'utf8')
const BASE = process.env.BASE_URL ?? env.match(/^APP_URL=(.*)$/m)?.[1]?.replace(/"/g, '') ?? 'http://localhost:8000'

const browser = await chromium.launch({ headless: !process.env.HEADED })
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms))

// One person's visit in a browser of their own (its own tab storage, so its own recording).
async function visit(email, steps) {
    const context = await browser.newContext({ baseURL: BASE, viewport: { width: 1440, height: 900 }, ignoreHTTPSErrors: true })
    const page = await context.newPage()
    await page.goto('/login')
    await page.fill('input[name=email]', email)
    await page.fill('input[name=password]', 'password')
    await Promise.all([page.waitForURL('**/shop'), page.click('form button')])

    const person = human(page)
    await sleep(1200)
    await steps(page, person)
    await sleep(1500)

    await page.goto('about:blank') // leaving uploads the rest
    await sleep(1500)
    await context.close()
    console.log(`recorded ${email}`)
}

// A pointer that moves and types like a person, so the replay looks like one.
function human(page) {
    let x = 700
    let y = 450

    const moveTo = async (locator) => {
        await locator.scrollIntoViewIfNeeded()
        const box = await locator.boundingBox()
        const tx = box.x + box.width * (0.35 + Math.random() * 0.3)
        const ty = box.y + box.height * (0.4 + Math.random() * 0.2)
        await page.mouse.move(tx, ty, { steps: 18 + Math.round(Math.hypot(tx - x, ty - y) / 40) })
        ;[x, y] = [tx, ty]
        await sleep(250)
    }

    return {
        moveTo,
        async click(locator) {
            await moveTo(locator)
            await page.mouse.down()
            await sleep(70)
            await page.mouse.up()
            await sleep(500)
        },
        async type(locator, text) {
            await this.click(locator)
            for (const char of text) {
                await page.keyboard.type(char)
                await sleep(60 + Math.random() * 90 + (char === ' ' ? 120 : 0))
            }
            await sleep(300)
        },
        async rage(locator, times = 4) {
            await moveTo(locator)
            for (let i = 0; i < times; i++) {
                await page.mouse.down()
                await sleep(40)
                await page.mouse.up()
                await sleep(110)
            }
        },
    }
}

const product = (page, name) => page.locator('div.rounded-xl', { hasText: name }).getByRole('button', { name: 'Add to cart' })

// Marcus buys a lamp, and it goes through.
await visit('marcus@acme.test', async (page, p) => {
    await p.click(page.getByRole('link', { name: 'Desk Lamp' }).last())
    await sleep(1500)
    await p.click(page.getByRole('button', { name: 'Add to cart' }))
    await p.click(page.getByRole('link', { name: /Cart/ }))
    await p.type(page.locator('textarea[name=address]'), '18 Harbour Street, Leith')
    await p.type(page.locator('input[name=card]'), '4242 4242 4242 4242')
    await p.type(page.locator('input[name=expiry]'), '04 / 29')
    await p.type(page.locator('input[name=cvc]'), '314')
    await p.click(page.locator('[data-pay]'))
    await page.waitForURL('**/orders/*')
    await sleep(2000)
})

// Priya looks around and leaves.
await visit('priya@acme.test', async (page, p) => {
    await p.click(page.getByRole('link', { name: 'Monitor Stand' }).last())
    await sleep(2500)
    await p.click(page.getByRole('link', { name: '← Shop' }))
    await p.click(page.getByRole('link', { name: 'Wireless Mouse' }).last())
    await sleep(2000)
})

// Tom reorders notebooks.
await visit('tom@acme.test', async (page, p) => {
    await p.click(page.getByRole('link', { name: 'Orders' }))
    await sleep(1200)
    await p.click(page.getByRole('link', { name: 'Shop' }))
    await p.click(product(page, 'A5 Dotted Notebook'))
    await p.click(product(page, 'Cable Organizer'))
    await p.click(page.getByRole('link', { name: /Cart/ }))
    await p.type(page.locator('textarea[name=address]'), 'Unit 4, 9 Mill Lane, Leeds')
    await p.type(page.locator('input[name=card]'), '4242 4242 4242 4242')
    await p.type(page.locator('input[name=expiry]'), '11 / 27')
    await p.type(page.locator('input[name=cvc]'), '208')
    await p.click(page.locator('[data-pay]'))
    await page.waitForURL('**/orders/*')
    await sleep(1500)
})

// Olivia: a pen and a notebook, a card that fails at the provider, a Pay button that seems dead.
await visit('olivia@acme.test', async (page, p) => {
    await p.moveTo(page.getByRole('link', { name: 'A5 Dotted Notebook' }).last())
    await p.click(product(page, 'A5 Dotted Notebook'))
    await p.click(product(page, 'Steel Fountain Pen'))
    await p.click(page.getByRole('link', { name: /Cart/ }))
    await sleep(800)
    await p.type(page.locator('textarea[name=address]'), '42 Elm Road, Bristol BS1 4DJ')
    await p.type(page.locator('input[name=card]'), '4000 0000 0000 0119')
    await p.type(page.locator('input[name=expiry]'), '09 / 28')
    await p.type(page.locator('input[name=cvc]'), '512')

    const pay = page.locator('[data-pay]')
    await p.click(pay)
    await sleep(2600) // the spinner stops; nothing else happens
    await p.rage(pay)
    await sleep(2500)

    await p.click(page.getByRole('button', { name: 'Report a problem' }))
    await p.type(page.locator('dialog textarea'), 'Pay button does nothing, tried a few times')
    await p.click(page.locator('dialog').getByRole('button', { name: 'Send' }))
    await sleep(1500)
    await p.click(page.locator('dialog [data-report-thanks]').getByRole('button', { name: 'Close' }))
})

await browser.close()
