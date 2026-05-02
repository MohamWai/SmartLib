//fun page code (guess the sentence)
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
      '<div class="alert alert-success mt-3">Amazing! You hit the correct answer.</div>';
    return true;
  } else {
    feadback.innerHTML =
      '<div class="alert alert-danger mt-3">Incorrect. Try again! Remember to use all words in correct order.</div>';
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



// ===== admin.html demo only (matches DB column names) — skipped on admin.php =====
(function () {
    function showError(id, msg) {
        const el = document.getElementById(id);
        const err = document.getElementById(id + "Error");
        if (!el || !err) return;
        el.classList.add("is-invalid");
        err.innerText = msg;
    }

    function clearErrors(ids) {
        ids.forEach((id) => {
            const el = document.getElementById(id);
            const err = document.getElementById(id + "Error");
            if (el) el.classList.remove("is-invalid");
            if (err) err.innerText = "";
        });
    }

    class BookRow {
        constructor(title, author, category, summary) {
            this.title = title;
            this.author = author;
            this.category = category;
            this.summary = summary;
        }
    }

    class UserRow {
        constructor(name, age, role, email) {
            this.name = name;
            this.age = age;
            this.role = role;
            this.email = email;
        }
    }

    let books = [];
    let users = [];

    const addBookBtn = document.getElementById("addBookBtn");
    const bTitle = document.getElementById("bTitle");
    const bAuthor = document.getElementById("bAuthor");
    const bCat = document.getElementById("bCat");
    const bSummary = document.getElementById("bSummary");
    const bookList = document.getElementById("bookList");

    if (addBookBtn && bTitle && bookList) {
        function validateBook(t, au, c) {
            clearErrors(["bTitle", "bAuthor", "bCat", "bSummary"]);
            let valid = true;
            if (t.trim() === "" || !isNaN(t)) {
                showError("bTitle", "Enter a valid title");
                valid = false;
            }
            if (au.trim() === "" || !isNaN(au)) {
                showError("bAuthor", "Enter a valid author");
                valid = false;
            }
            if (c.trim() === "" || !isNaN(c)) {
                showError("bCat", "Enter a valid category");
                valid = false;
            }
            return valid;
        }

        function showBooks() {
            let txt = "";
            books.forEach((p, index) => {
                const sum =
                    p.summary.length > 40 ? p.summary.slice(0, 40) + "…" : p.summary;
                txt += `
        <tr>
            <td>${p.title}</td>
            <td>${p.author}</td>
            <td>${p.category}</td>
            <td class="small text-start">${sum}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" data-book-idx="${index}">
                    Delete
                </button>
            </td>
        </tr>`;
            });
            bookList.innerHTML = txt;
            bookList.querySelectorAll("[data-book-idx]").forEach((btn) => {
                btn.onclick = function () {
                    const i = Number(btn.getAttribute("data-book-idx"));
                    if (confirm("Remove this row?")) {
                        books.splice(i, 1);
                        showBooks();
                    }
                };
            });
        }

        addBookBtn.onclick = function () {
            const t = bTitle.value;
            const au = bAuthor.value;
            const c = bCat.value;
            const s = bSummary.value.trim();
            if (!validateBook(t, au, c)) return;
            books.push(new BookRow(t.trim(), au.trim(), c.trim(), s));
            showBooks();
            bTitle.value = "";
            bAuthor.value = "";
            bCat.value = "";
            bSummary.value = "";
        };
    }

    const addStudentBtn = document.getElementById("addStudentBtn");
    const sName = document.getElementById("sName");
    const sAge = document.getElementById("sAge");
    const sRole = document.getElementById("sRole");
    const sEmail = document.getElementById("sEmail");
    const studentList = document.getElementById("studentList");

    if (addStudentBtn && sName && studentList) {
        function validateUserRow(n, ageStr, role, email) {
            clearErrors(["sName", "sAge", "sRole", "sEmail"]);
            let valid = true;
            if (n.trim() === "" || !isNaN(n)) {
                showError("sName", "Enter a valid name");
                valid = false;
            }
            if (ageStr.trim() !== "") {
                const a = Number(ageStr);
                if (isNaN(a) || a < 1 || a > 120) {
                    showError("sAge", "Age must be 1–120 or leave blank (NULL)");
                    valid = false;
                }
            }
            if (role.trim() === "" || !isNaN(role)) {
                showError("sRole", "Enter a valid role");
                valid = false;
            }
            if (email.trim() === "" || !email.includes("@")) {
                showError("sEmail", "Enter a valid email");
                valid = false;
            }
            return valid;
        }

        function showUserRows() {
            let txt = "";
            users.forEach((u, index) => {
                const ageDisp =
                    u.age === "" || u.age == null ? "—" : String(u.age);
                txt += `
        <tr>
            <td>${u.name}</td>
            <td>${ageDisp}</td>
            <td>${u.role}</td>
            <td class="small">${u.email}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" data-user-idx="${index}">
                    Delete
                </button>
            </td>
        </tr>`;
            });
            studentList.innerHTML = txt;
            studentList.querySelectorAll("[data-user-idx]").forEach((btn) => {
                btn.onclick = function () {
                    const i = Number(btn.getAttribute("data-user-idx"));
                    if (confirm("Remove this row?")) {
                        users.splice(i, 1);
                        showUserRows();
                    }
                };
            });
        }

        addStudentBtn.onclick = function () {
            const n = sName.value;
            const ageStr = sAge.value.trim();
            const role = sRole ? sRole.value : "";
            const email = sEmail ? sEmail.value : "";
            if (!validateUserRow(n, ageStr, role, email)) return;
            const ageVal = ageStr === "" ? "" : Number(ageStr);
            users.push(new UserRow(n.trim(), ageVal, role.trim(), email.trim()));
            showUserRows();
            sName.value = "";
            sAge.value = "";
            if (sRole) sRole.value = "";
            if (sEmail) sEmail.value = "";
        };
    }

    const uploadBtn = document.getElementById("uploadBtn");
    const uTitle = document.getElementById("uTitle");
    const uAuthor = document.getElementById("uAuthor");
    const uCategory = document.getElementById("uCategory");
    const uSummary = document.getElementById("uSummary");
    const uFile = document.getElementById("uFile");

    if (uploadBtn && uTitle && uAuthor) {
        uploadBtn.onclick = function () {
            clearErrors(["uTitle", "uAuthor", "uCategory", "uSummary", "uFile"]);
            let valid = true;
            const t = uTitle.value;
            const a = uAuthor.value;
            const cat = uCategory ? uCategory.value : "";
            const s = uSummary ? uSummary.value : "";

            if (t.trim() === "" || !isNaN(t)) {
                showError("uTitle", "Enter a valid title");
                valid = false;
            }
            if (a.trim() === "" || !isNaN(a)) {
                showError("uAuthor", "Enter a valid author");
                valid = false;
            }
            if (!uCategory || cat.trim() === "" || !isNaN(cat)) {
                if (uCategory) showError("uCategory", "Category is required");
                valid = false;
            }
            if (!valid) return;

            alert("Demo only — use admin.php to save to the database.");
            uTitle.value = "";
            uAuthor.value = "";
            if (uCategory) uCategory.value = "";
            if (uSummary) uSummary.value = "";
            if (uFile) uFile.value = "";
        };
    }
})();


