function scorePassword(password) {


    let password_strength = document.getElementById("password_strength");


    //if textBox is empty
    if (password.length === 0) {
        password_strength.innerHTML = "";
        return;
    }

    //if textBox is empty
    if (password.length < 6) {
        password_strength.innerHTML = "Parolanız en az 6 karakterden oluşmalıdır.";
        password_strength.style.color = 'Red';
        return;
    }

    //Regular Expressions
    let regex = [];
    regex.push("[A-Z]"); //For Uppercase Alphabet
    regex.push("[a-z]"); //For Lowercase Alphabet
    regex.push("[0-9]"); //For Numeric Digits
    regex.push("[$@$!%*#?&]"); //For Special Characters

    let passed = 0;

    //Validation for each Regular Expression
    for (let i = 0; i < regex.length; i++) {
        if ((new RegExp(regex[i])).test(password)) {
            passed++;
        }
    }

    //Validation for Length of Password
    if (passed > 2 && password.length > 8) {
        passed++;
    }

    //Display of Status
    let color = "";
    let passwordStrength = "";

    switch (passed) {
        case 0:
            break;
        case 1:
            passwordStrength = "Parolanız zayıf.";
            color = "Red";
            break;
        case 2:
            passwordStrength = "Parolanız iyi.";
            color = "darkorange";
            break;
        case 3:
            break;
        case 4:
            passwordStrength = "Parolanız güçlü.";
            color = "Green";
            break;
        case 5:
            passwordStrength = "Parolanız çok güçlü.";
            color = "darkgreen";
            break;
    }
    password_strength.innerHTML = passwordStrength;
    password_strength.style.color = color;
}


export {scorePassword};