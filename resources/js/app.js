const csrf = () => document.querySelector('meta[name=csrf-token]')?.content

// Checkout. The bug this demo is about: only a redirect and a validation error are handled, so when the
// payment provider fails the spinner stops and nothing else happens. The replay shows what the customer
// went through; the error marker and its report id lead to the exception in the logs.
const checkout = document.querySelector('[data-checkout]')
let paying = false

async function pay() {
    if (paying) return

    const spinner = checkout.querySelector('[data-spinner]')
    const errors = checkout.querySelector('[data-errors]')

    paying = true
    spinner.hidden = false
    errors.hidden = true

    const response = await fetch(checkout.action, {
        method: 'POST',
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: new FormData(checkout),
    })

    paying = false
    spinner.hidden = true

    if (response.ok) {
        window.location = (await response.json()).redirect
    } else if (response.status === 422) {
        const { errors: messages } = await response.json()
        errors.replaceChildren(...Object.values(messages).flat().map((message) => Object.assign(document.createElement('li'), { textContent: message })))
        errors.hidden = false
    }
}

checkout?.querySelector('[data-pay]').addEventListener('click', pay)
checkout?.addEventListener('submit', (event) => {
    event.preventDefault()
    pay()
})

// "Report a problem": SessionReplay.send() stores a `report` marker at this moment and uploads what the
// recorder holds, and the server dispatches ReplayReported (see SessionReplayServiceProvider).
const dialog = document.querySelector('[data-report]')

document.querySelector('[data-report-open]')?.addEventListener('click', () => {
    dialog.querySelector('[data-report-form]').hidden = false
    dialog.querySelector('[data-report-thanks]').hidden = true
    dialog.showModal()
})
dialog?.querySelectorAll('[data-report-close]').forEach((button) => button.addEventListener('click', () => dialog.close()))
dialog?.querySelector('[data-report-form]').addEventListener('submit', async (event) => {
    event.preventDefault()
    const form = event.target
    await window.SessionReplay?.send(form.message.value)
    form.reset()
    form.hidden = true
    dialog.querySelector('[data-report-thanks]').hidden = false
})
