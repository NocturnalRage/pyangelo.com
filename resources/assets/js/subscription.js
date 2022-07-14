/* global Stripe:false */
const form = document.getElementById('payment-form')
const stripePublishableKey = form.getAttribute('data-stripe-publishable-key')
const stripe = Stripe(stripePublishableKey)
const messageContainer = document.querySelector('#stripe-error-message')

const crsfToken = document.getElementById('payment-form').getAttribute('data-crsf-token')
const priceId = document.getElementById('payment-form').getAttribute('data-price-id')
const data = 'crsfToken=' + encodeURIComponent(crsfToken) + '&priceId=' + encodeURIComponent(priceId)
const options = {
  method: 'POST',
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  },
  body: data
}
fetch('/process-subscription', options)
  .then(response => response.json())
  .then(stripeData => {
    if (stripeData.status === 'login-error') {
      window.location.href = '/login'
    } else if (stripeData.status === 'active-subscription') {
      window.location.href = '/subscription'
    } else if (stripeData.status === 'crsf-error') {
      window.location.href = '/choose-plan'
    } else if (stripeData.status === 'post-error') {
      window.location.href = '/choose-plan'
    } else if (stripeData.status === 'stripe-price-error') {
      window.location.href = '/choose-plan'
    } else if (stripeData.status === 'pyangelo-error') {
      window.location.href = '/choose-plan'
    } else if (stripeData.status === 'stripe-error') {
      messageContainer.textContent = stripeData.message
    } else if (stripeData.status === 'success') {
      const clientSecret = stripeData.clientSecret
      const payButton = document.getElementById('pay-btn')

      const appearance = {
        theme: 'night',
        labels: 'floating'
      }

      // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in step 5
      const elements = stripe.elements({ clientSecret, appearance })

      // Create and mount the Payment Element
      const paymentElement = elements.create('payment')
      paymentElement.mount('#payment-element')

      payButton.addEventListener('click', async (e) => {
        e.preventDefault()
        payButton.disabled = true
        const { error } = await stripe.confirmPayment({
          // `Elements` instance that was used to create the Payment Element
          elements,
          confirmParams: {
            return_url: window.location.origin + '/premium-member-welcome'
          }
        })

        if (error) {
          // This point will only be reached if there is an immediate error when
          // confirming the payment. Show error to your customer (for example, payment
          // details incomplete)
          messageContainer.textContent = error.message
        } else {
          // Your customer will be redirected to your `return_url`. For some payment
          // methods like iDEAL, your customer will be redirected to an intermediate
          // site first to authorize the payment, then redirected to the `return_url`.
        }
        payButton.disabled = false
      })
      payButton.disabled = false
    }
  })
  .catch((error) => { console.error('Error: ', error) })
