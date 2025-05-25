function validateForm() {
    let valid = true;
    let oldStars = document.getElementsByClassName("error");
    while (oldStars.length > 0) {
        oldStars[0].remove();
    }
    let inputs = document.querySelectorAll("input[type='text'], input[type='email'], input[type='password'], input[type='number'], input[type='date']");
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value.trim() === "") {
            let star = document.createElement("span");
            star.className = "error";
            star.style.color = "red";
            star.innerHTML = " *";
            inputs[i].parentNode.appendChild(star);
            valid = false;
        }
    }
    let address = document.querySelector("textarea[name='address']");
    if (address.value.trim() === "") {
        let star = document.createElement("span");
        star.className = "error";
        star.style.color = "red";
        star.innerHTML = " *";
        address.parentNode.appendChild(star);
        valid = false;
    }
    let male = document.querySelector("input[value='Male']");
    let female = document.querySelector("input[value='Female']");
    if (!male.checked && !female.checked) {
        let star = document.createElement("span");
        star.className = "error";
        star.style.color = "red";
        star.innerHTML = " *";
        male.parentNode.appendChild(star);
        valid = false;
    }
    let department = document.querySelector("select[name='department']");
    if (department.value === "") {
        let star = document.createElement("span");
        star.className = "error";
        star.style.color = "red";
        star.innerHTML = " *";
        department.parentNode.appendChild(star);
        valid = false;
    }
    return valid;
}
window.onload = function () {
    document.querySelector("form").onsubmit = function () {
        return validateForm();
    };
};