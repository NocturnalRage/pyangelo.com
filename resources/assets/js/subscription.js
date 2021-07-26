const style = {
  base: {
    iconColor: '#666ee8',
    color: '#31325f',
    fontWeight: 400,
    fontFamily:
      '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '15px',
    '::placeholder': {
      color: '#aab7c4',
    },
    ':-webkit-autofill': {
      color: '#666ee8',
    },
  },
};
let card = elements.create('card', { style });
let errorDiv = document.getElementById('card-element-errors');

card.mount('#card-element');
card.on('change', function (event) {
  displayError(event);
});
card.focus();
function displayError(event) {
  if (event.error) {
    errorDiv.style.display = "block";
    errorDiv.textContent = event.error.message;
  } else {
    errorDiv.style.display = "none";
    errorDiv.textContent = '';
  }
}
const paymentButton = document.getElementById('submit-payment-btn');
paymentButton.addEventListener('click', async (e) => {
  e.preventDefault();
  paymentButton.disabled = true;
  const crsfToken = document.getElementById('payment-form').getAttribute('data-crsf-token');
  const priceId = document.getElementById('payment-form').getAttribute('data-price-id');
  const data = "crsfToken=" + encodeURIComponent(crsfToken) + "&priceId=" + encodeURIComponent(priceId);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/process-subscription', options)
    .then(response => response.json())
    .then(data => {
      // Create payment method and confirm payment intent.
      stripe.confirmCardPayment(data.clientSecret, {
        payment_method: {
          card: card,
          billing_details: {
            name: data.customerName,
          },
        }
      }).then((result) => {
        if(result.error) {
          errorDiv.style.display = "block";
          console.log(result.error.message);
          const displayErrorMessage = result.error.message + "\nPlease fix the error and try the payment again!";
          errorDiv.textContent = displayErrorMessage;
          paymentButton.disabled = false;
        } else {
          // Successful subscription payment
          window.location.href = '/premium-member-welcome';
        }
      });
    })
    .catch((error) => { console.error('Error: ', error); });
});
