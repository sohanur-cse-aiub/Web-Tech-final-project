function validateForm() {
    let valid = true;
    let oldStars = document.getElementsByClassName("error");
    while (oldStars.length > 0) {
        oldStars[0].remove();
    }
    let inputs = document.querySelectorAll("input[type='text'], input[type='email'], input[type='password']");
    for (let i = 0; i < inputs.length; i++) {
        if (inputs[i].value === "") {
            let star = document.createElement("span");
            star.className = "error";
            star.style.color = "red";
            star.innerHTML = " *";
            inputs[i].parentNode.appendChild(star);
            valid = false;
        }
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
    let role = document.getElementById("role");
    if (role.value === "") {
        let star = document.createElement("span");
        star.className = "error";
        star.style.color = "red";
        star.innerHTML = " *";
        role.parentNode.appendChild(star);
        valid = false;
    }
    let question = document.querySelector("select[name='security_question']");
    if (question.value === "") {
        let star = document.createElement("span");
        star.className = "error";
        star.style.color = "red";
        star.innerHTML = " *";
        question.parentNode.appendChild(star);
        valid = false;
    }
    return valid;
}
window.onload = function () {
    document.querySelector("form").onsubmit = function () {
        return validateForm();
    };
};