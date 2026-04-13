// ===== Classes =====
class Product {
    constructor(name, price, category) {
        this.name = name;
        this.price = price;
        this.category = category;
    }
}

class Student {
    constructor(name, age, major) {
        this.name = name;
        this.age = age;
        this.major = major;
    }
}

// ===== Arrays =====
let products = [];
let students = [];

// ===== Elements =====
const pName = document.getElementById("pName");
const pPrice = document.getElementById("pPrice");
const pCat = document.getElementById("pCat");

const sName = document.getElementById("sName");
const sAge = document.getElementById("sAge");
const sMajor = document.getElementById("sMajor");

// ===== Validation =====
function showError(id, msg) {
    document.getElementById(id).classList.add("is-invalid");
    document.getElementById(id + "Error").innerText = msg;
}

function clearErrors(ids) {
    ids.forEach(id => {
        document.getElementById(id).classList.remove("is-invalid");
        document.getElementById(id + "Error").innerText = "";
    });
}

function validateProduct(n, p, c) {
    clearErrors(["pName", "pPrice", "pCat"]);
    let valid = true;

    if (n.trim() === "" || !isNaN(n)) {
        showError("pName", "Enter valid name");
        valid = false;
    }

    if (p === "" || isNaN(p) || p <= 0 || p > 10000) {
        showError("pPrice", "Price must be 1-10000");
        valid = false;
    }

    if (c.trim() === "" || !isNaN(c)) {
        showError("pCat", "Enter valid category");
        valid = false;
    }

    return valid;
}

function validateStudent(n, a, m) {
    clearErrors(["sName", "sAge", "sMajor"]);
    let valid = true;

    if (n.trim() === "" || !isNaN(n)) {
        showError("sName", "Enter valid name");
        valid = false;
    }

    if (a === "" || isNaN(a) || a < 16 || a > 100) {
        showError("sAge", "Age must be 16-60");
        valid = false;
    }

    if (m.trim() === "" || !isNaN(m)) {
        showError("sMajor", "Enter valid major");
        valid = false;
    }

    return valid;
}

// ===== Add =====
document.getElementById("addProductBtn").onclick = function () {
    let n = pName.value;
    let p = Number(pPrice.value);
    let c = pCat.value;

    if (!validateProduct(n, p, c)) return;

    products.push(new Product(n, p, c));
    showProducts();

    pName.value = "";
    pPrice.value = "";
    pCat.value = "";
};

document.getElementById("addStudentBtn").onclick = function () {
    let n = sName.value;
    let a = Number(sAge.value);
    let m = sMajor.value;

    if (!validateStudent(n, a, m)) return;

    students.push(new Student(n, a, m));
    showStudents();

    sName.value = "";
    sAge.value = "";
    sMajor.value = "";
};

// ===== Delete =====
function deleteProduct(index) {
    if (confirm("Are you sure?")) {
        products.splice(index, 1);
        showProducts();
    }
}

function deleteStudent(index) {
    if (confirm("Are you sure?")) {
        students.splice(index, 1);
        showStudents();
    }
}

// ===== Display =====
function showProducts() {
    let txt = "";

    products.forEach((p, index) => {
        txt += `
        <tr>
            <td>${p.name}</td>
            <td>${p.price}</td>
            <td>${p.category}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="deleteProduct(${index})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("productList").innerHTML = txt;
}

function showStudents() {
    let txt = "";

    students.forEach((s, index) => {
        txt += `
        <tr>
            <td>${s.name}</td>
            <td>${s.age}</td>
            <td>${s.major}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="deleteStudent(${index})">
                    Delete
                </button>
            </td>
        </tr>`;
    });

    document.getElementById("studentList").innerHTML = txt;
}
// ===== Upload Validation =====
const uTitle = document.getElementById("uTitle");
const uAuthor = document.getElementById("uAuthor");
const uSummary = document.getElementById("uSummary");
const uFile = document.getElementById("uFile");

document.getElementById("uploadBtn").onclick = function () {

    clearErrors(["uTitle", "uAuthor", "uSummary", "uFile"]);

    let valid = true;

    let t = uTitle.value;
    let a = uAuthor.value;
    let s = uSummary.value;
    let f = uFile.value;

    if (t.trim() === "" || !isNaN(t)) {
        showError("uTitle", "Enter valid title");
        valid = false;
    }

    if (a.trim() === "" || !isNaN(a)) {
        showError("uAuthor", "Enter valid author");
        valid = false;
    }

    if (s.trim() === "" || s.length < 10) {
        showError("uSummary", "Summary too short");
        valid = false;
    }

    if (f === "") {
        showError("uFile", "Please choose a file");
        valid = false;
    }

    if (!valid) return;

    alert("Book uploaded successfully!");

    // clear inputs
    uTitle.value = "";
    uAuthor.value = "";
    uSummary.value = "";
    uFile.value = "";
};

//fun page code (jess the sentence)
const sentences = [
        "Reading opens the door to new worlds",
        "A library is a treasure chest of knowledge",
        "Books are windows to the past",
        "Every book holds a unique adventure",
        "Words have the power to change minds",
      ]

const words_box = document.getElementById("wordsContainer");
let currentQNum = 0;

function startGame() {
  let currentQNum = 0;
  display_words(currentQNum);
  displayQuestionNum(currentQNum +1)
  document.getElementById("answer_area").value = ""; // Clear answer area
  document.getElementById("feadback").innerHTML = ""; // Clear feedback
}

function displayQuestionNum(qnum){
  document.getElementById("q_num").textContent = "Q: "+qnum+" of 5"
}

function nextQuestion() {
  // First check if the current answer is correct
  if (checkAns()) {
    // If correct, move to next question
    currentQNum++;
    if (currentQNum < sentences.length) {
      displayQuestionNum(currentQNum+1)
      display_words(currentQNum);
      document.getElementById("answer_area").value = ""; // Clear answer area
      document.getElementById("feadback").innerHTML = ""; // Clear feedback
    } else {
      // Game completed
      document.getElementById("feadback").innerHTML =
        '<div class="alert alert-success mt-3">Congratulations! You completed all sentences!</div>';
    }
  } else {
    // If not correct, show message and stay on same question
    document.getElementById("feadback").innerHTML =
      '<div class="alert alert-warning mt-3">Please answer correctly before moving to the next question!</div>';
  }
}

function display_words(qNum) {
  words_box.replaceChildren();
  words = unordringWords(qNum);

  words.forEach((word) => {
    // Create a box div for each word
    const box = document.createElement("div");
    box.className = "btn btn-primary col m-3";
    box.textContent = word;

    // Add the box to the container
    words_box.appendChild(box);
  });
}

function checkAns() {
  const currectAns = sentences[currentQNum].trim(); //get the current sentence
  const playerAns = document.getElementById("answer_area").value.trim();//get what player Entered
  const feadback = document.getElementById("feadback");
  if (playerAns == currectAns) { //is secnteces match ??
    feadback.innerHTML =
      '<div class="alert alert-success mt-3">Amazing! You hit the currect answer.</div>';
    return true;
  } else {
    feadback.innerHTML =
      '<div class="alert alert-danger mt-3">Incurrect. Try again! Remember to use all words in correct order.</div>';
    return false;
  }
}

function unordringWords(num) { //randomaize the words order by swaping 
  words = sentences[num].trim().split(" ");

  for (let i = 0; i < words.length; i++) {
    const randI = Math.floor(Math.random() * words.length);
    const randJ = Math.floor(Math.random() * words.length);
    swap(words, randI, randJ);
  }

  return words;
}

function swap(arr, i, j) { 
  let temp = arr[i];
  arr[i] = arr[j];
  arr[j] = temp;
}




//this for bootstrab validation styles
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()

//the time and date in the moving panner
function updateTime() {
    const now = new Date();
    document.getElementById('date').innerText = now.toLocaleDateString();
    document.getElementById('time').innerText = now.toLocaleTimeString();
  }
  updateTime(); // Run once on load
  setInterval(updateTime, 1000); // Update every second
