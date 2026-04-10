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