console.log("Bootstrap object:", bootstrap);
var myModal = new bootstrap.Modal(document.getElementById("accountModal"));

const items = [
  "New sport shoes 50% off this summers school year",
  "New buyers get up to 20% off first purchase",
  "Bundle purchases this, premium quality",
  "Santisation quality control certifcations below",
   "More amazing stuff coming soon!!",
   "Cannot wait to see you all there!"
];

const horizontalTextScroll = document.querySelectorAll(".text-scroll");

function initializeItems() {
  horizontalTextScroll.forEach((scrollItem) => {
    scrollItem.innerHTML = "";
  });
}

initializeItems();

items.forEach((item) => {
  addItemToScrolls(item);
});

function addItemToScrolls(text) {
 
  horizontalTextScroll.forEach((scrollItem) => {
    const textItem = document.createElement("span");
    textItem.classList.add("text-item");
    textItem.innerHTML = text;
    scrollItem.appendChild(textItem);

    const star = createStar();
    star.classList.add("star");
    scrollItem.appendChild(star);
  });
}

function createStar() {
  const star = document.createElement("span");
  star.textContent = "★";
  return star;
} 




// Existing password toggle
document.getElementById('togglePassword').addEventListener('click', function()
{
  const pwd = document.getElementById('loginPassword');
  if (pwd.type ==='password')
     {
     pwd.type = 'text';
       this.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } 
  else 
    {
    pwd.type = 'password';
    this.innerHTML = '<i class="bi bi-eye-slash"></i>';
  }
});

 function showWelcomeToast(message = "Welcome back!") 
{
  const body = document.querySelector('#welcomeToast .toast-body');
  body.innerHTML = `<i class="bi bi-check-circle me-2"></i>${message}`;
  var toastEl = document.getElementById('welcomeToast');
  var toast = new bootstrap.Toast(toastEl);
  toast.show();
  setTimeout(() => toast.hide(), 2500);
} 

function showWelcomeToast(message = "Welcome back!") {
    const toastElement = document.getElementById("welcomeToast");

    if (!toastElement) {
        console.error("Toast element with id 'welcomeToast' was not found.");
        return;
    }

    const toastBody = toastElement.querySelector(".toast-body");

    if (!toastBody) {
        console.error("Toast body was not found.");
        return;
    }

    toastBody.innerHTML =
        '<i class="bi bi-check-circle me-2"></i>' + message;

    const toast = bootstrap.Toast.getOrCreateInstance(toastElement, {
        delay: 4000
    });

    toast.show();
}

document.getElementById("signupForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    formData.set(
        "terms_accepted",
        document.getElementById("agreeTerms").checked ? "1" : "0"
    );

    try {
        const response = await fetch("signup.php", {
            method: "POST",
            body: formData
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            alert(data.message || "Sign-up failed.");
            return;
        }

        form.reset();
        myModal.hide(); 
        showWelcomeToast(data.message || "Your account was created successfully.");

    } catch (error) {
        console.error("Sign-up error:", error);
        alert("An unexpected error occurred: " + error.message);
    }
});

// Tracks which Google button the user clicked
let googleAction = "login";

// Google Identity Services
window.addEventListener("load", function () {
    const googleClientId = document
        .querySelector('meta[name="google-signin-client_id"]')
        .content;

    if (!window.google || !google.accounts) {
        console.error("Google Identity Services did not load.");
        return;
    }

    google.accounts.id.initialize({
        client_id: googleClientId,
        callback: handleGoogleResponse
    });

    // Google login button
    google.accounts.id.renderButton(
        document.getElementById("googleLoginBtn"),
        {
            theme: "outline",
            size: "large",
            text: "continue_with",
            click_listener: function () {
                googleAction = "login";
                console.log("Google LOGIN clicked");
            }
        }
    );

    // Google sign-up button
    google.accounts.id.renderButton(
        document.getElementById("googleSignupBtn"),
        {
            theme: "filled_blue",
            size: "large",
            text: "signup_with",
            click_listener: function () {
                googleAction = "signup";
                console.log("Google SIGNUP clicked");
            }
        }
    );
});

// One callback for both Google buttons
function handleGoogleResponse(response) {
    const idToken = response.credential;

    let endpoint = "";

    if (googleAction === "signup") {
        endpoint = "google-signup.php";
    } else {
        endpoint = "google-login.php";
    }

    console.log("Sending Google token to:", endpoint);

    fetch(endpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            id_token: idToken
        })
    })
    .then(async (res) => {
        const data = await res.json();

        if (!res.ok || !data.success) {
            alert(data.message || "Google request failed.");
            return;
        }

        myModal.hide();

        if (googleAction === "signup") {
            showWelcomeToast(
                data.message || "Your Google account was created successfully."
            );
            
        } else {
            showWelcomeToast(
                data.message || "You are logged in with Google."
            );
        }
    })
    .catch((error) => {
        console.error("Google error:", error);
        alert("Google error: " + error.message);
    });
}

document.addEventListener("DOMContentLoaded", function () {

    const loginForm = document.getElementById("loginForm");

    if (!loginForm) {
        console.error("Login form not found.");
        return;
    }

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const email = document.getElementById("loginEmail").value.trim();
        const password = document.getElementById("loginPassword").value;

        if (!email || !password) {
            alert("Please enter your email address and password.");
            return;
        }

        const formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        fetch("login.php", {
            method: "POST",
            body: formData
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    alert(data.message);
                    window.location.href = "index.php";
                } else {
                    alert(data.message || "Invalid email address or password.");
                }
            })
            .catch(function (error) {
                console.error(error);
                alert("Login request failed. Please try again.");
            });
    });

    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("loginPassword");

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
            const icon = togglePassword.querySelector("i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            } else {
                passwordInput.type = "password";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            }
        });
    }
});







