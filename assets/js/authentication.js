
  const baseURL = "./includes/auth/" // Set base to the folder directory


async function registerUser(event) {
  // Prevent default form submission reload
  event.preventDefault();

  const dataToSend = {
    name: document.getElementById("name").value,
    matric_number: document.getElementById("matric_number").value,
    password: document.getElementById("password").value,
  };

  try {
    // Append file name to baseURL, or specify full relative path cleanly
    const response = await axios.post(`${baseURL}authentication.php`, dataToSend);
    
    console.log("Registration successful:", response.data);
    alert("Account created successfully!");
    
    // Optional: redirect user after success
    // window.location.href = "./login.php";

  } catch (error) {
    console.error("Error registering user:", error.response?.data || error.message);
    alert("Registration failed. Please try again.");
  }
}