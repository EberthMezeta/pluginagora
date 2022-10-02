const FORM = document.getElementsByClassName('mform')[0];
const HiddenList = document.getElementsByName('fileslist')[0];

const TOKENHIDDEN = document.getElementsByName('token')[0];

const USER_INPUT = document.getElementById("id_usuario");
const PASSWORD_INPUT = document.getElementById("id_password");

const LOGIN_BTN = document.getElementById("id_loginBTN");
const LOGOUT_BTN = document.getElementById("id_logoutBTN");


const USERNAME_CONTAINER = document.getElementById("fitem_id_usuario");
const PASSWORD_CONTAINER = document.getElementById("fitem_id_password");
const LOGIN_BTN_CONTAINER = document.getElementById("fitem_id_loginBTN");
const LOGOUT_BTN_CONTAINER = document.getElementById("fitem_id_logoutBTN");

const SUBMIT_BUTTONAR_CONTAINER = document.getElementById("fgroup_id_buttonar");
const CHECKBOX_CONTAINERS = document.getElementsByClassName("checkbox");

console.log(FORM);

var ids = [];
var access_token = "";

FORM.addEventListener('submit', function(e) {
    e.preventDefault();


    if (ids.length == 0 && access_token == "") {
        return false;
    }

    let idsString = ids.join(',');
    HiddenList.value = idsString;
    FORM.submit();

});

FORM.addEventListener('click', function(e) {
    if (e.target.type === 'checkbox') {
        const checkbox = e.target;
        let idfile = checkbox.getAttribute("fileid");
  
        index = ids.indexOf(idfile);
  
        if (index > -1) {
            ids.splice(index, 1);
        } else {
            ids.push(idfile);
        }
    }
});



LOGIN_BTN.addEventListener("click", () => {
    //Logica del servicio
  
    loginService();
  });
  

  
  LOGOUT_BTN.addEventListener("click", () => {
    //cerrar sesion
  
    logoutService();
  });

  const loginService = async () => {
    try {
      let username = USER_INPUT.value;
      let password = PASSWORD_INPUT.value;
  
      let formData = new FormData();
      formData.append("username", username);
      formData.append("password", password);
  
      const response = await fetch(
        "http://localhost/moodle4/blocks/pluginagora/services/login.php",
        {
          method: "POST",
          body: formData,
        }
      );
      let data = await response.json();
  
      console.log(data);
  
      
  
      if (!data["status"]) {
        return;
      }

      console.log(data["access_token"]);
      access_token = data["access_token"];
      TOKENHIDDEN.value = access_token;

      showForm();
      hideLoginForm();
    } catch (error) {
      console.log(error);
    }
  };


  const logoutService = async () => {
    try {
      console.log(access_token);
  
      let formData = new FormData();
  
      formData.append("access_token", access_token);
  
      const response = await fetch(
        "http://localhost/moodle4/blocks/pluginagora/services/logout.php",
        {
          method: "POST",
          body: formData,
        }
      );
  
      let data = await response.json();
      console.log(data);
  
      if (!data["status"]) {
        return;
      }
  
      showLoginForm();
      hidenForm();
    } catch (error) {
      console.log(error);
    }
  };
  
  const hideLoginForm = async () => {
    USERNAME_CONTAINER.style.display = "none";
    PASSWORD_CONTAINER.style.display = "none";
    LOGIN_BTN_CONTAINER.style.display = "none";
    LOGOUT_BTN_CONTAINER.style.display = "";

    USER_INPUT.value = "";
    PASSWORD_INPUT.value = "";

  };
  
  const showLoginForm = async () => {
    USERNAME_CONTAINER.style.display = "";
    PASSWORD_CONTAINER.style.display = "";
    LOGIN_BTN_CONTAINER.style.display = "";
    LOGOUT_BTN_CONTAINER.style.display = "none";
  };


  const hidenForm = async () => {
    SUBMIT_BUTTONAR_CONTAINER.style.display = "none";
    CHECKBOX_CONTAINERS.forEach((element) => {
        element.style.display = "none";
    });
  }

  const showForm = async () => {
    SUBMIT_BUTTONAR_CONTAINER.style.display = "";
    CHECKBOX_CONTAINERS.forEach((element) => {
        element.style.display = "";
    });
  }

  hidenForm();
  showLoginForm();