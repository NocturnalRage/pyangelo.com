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
const updateBtn = document.getElementById('submit-details-btn');
updateBtn.addEventListener('click', async (e) => {
  e.preventDefault();
  updateBtn.disabled = true;
  const crsfToken = document.getElementById('payment-form').getAttribute('data-crsf-token');
  const data = "crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/payment-method-update', options)
    .then(response => response.json())
    .then(data => {
      // confirm card details.
      stripe.confirmCardSetup(data.clientSecret, {
        payment_method: {
          card: card,
          billing_details: {
            name: data.customerName,
          },
        }
      }).then((result) => {
        if(result.error) {
          errorDiv.style.display = "block";
          const displayErrorMessage = result.error.message + "\nPlease fix the error and try updating your card details again!";
          errorDiv.textContent = displayErrorMessage;
          updateBtn.disabled = false;
        } else {
          window.location.href = '/payment-method-updated';
        }
      });
    })
    .catch((error) => { console.error('Error: ', error); });
});
