<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Administrative</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
.sidebar {
  position: fixed;
  height: 100%;
  width: 260px;
  background: #d37a15;
  padding: 15px;
  z-index: 99;
}
#h2 {
  color: white;
  margin-top: 20px;
  margin-right: 5px;
  margin-left: 10px;
  display: flex;
  align-items: center;
}
.sidebar a {
  color: #fff;
  text-decoration: none;
}
.menu-content {
  position: relative;
  height: 80%;
  width: 100%;
  margin-top: 40px;
  overflow-y: scroll;
}
.menu-content::-webkit-scrollbar {
  display: none;
}
.menu-items {
  height: 100%;
  width: 100%;
  list-style: none;
  transition: all 0.4s ease;
}
.submenu-active .menu-items {
  transform: translateX(-56%);
}
.menu-title {
  color: whitesmoke;
  font-size: 18px;
  padding: 15px 20px;
}
.item a,
.submenu-item {
  padding: 20px;
  display: inline-block;
  width: 100%;
  border-radius: 12px;
}
.item i {
  font-size: 12px;
}
.item {
  display: flex;
  align-items: center;
}
.icon {
  color: white;
}
.icon i {
  font-size: 24px;
  margin-right: 10px;
}
.item a:hover,
.submenu-item:hover,
.submenu .menu-title:hover {
  background: rgba(255, 255, 255, 0.1);
}
.submenu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  cursor: pointer;
}
.submenu {
  position: absolute;
  height: 100%;
  width: 100%;
  top: 0;
  right: calc(-100% - 26px);
  height: calc(100% + 100vh);
  background: #rgba(255, 255, 255, 0.1);
  display: none;
}
.show-submenu ~ .submenu {
  display: block;
}
.submenu .menu-title {
  border-radius: 12px;
  cursor: pointer;
}
.submenu .menu-title i {
  margin-right: 10px;
}
.navbar,
.main {
  left: 260px;
  width: calc(100% - 260px);
  transition: all 0.5s ease;
  z-index: 500;
}
.sidebar.close ~ .navbar,
.sidebar.close ~ .main {
  left: 0;
  width: 100%;
}
.navbar {
  position: fixed;
  color: #0a0a0a;
  padding: 15px 20px;
  font-size: 25px;
  background: #rgba(255, 255, 255, 0.1);
  cursor: pointer;
}
.navbar #sidebar-close {
  cursor: pointer;
}
.main {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
  z-index: 100;
  background: #e7f2fd;
}
.main h1 {
  color: #11101d;
  font-size: 40px;
  text-align: center;
}
.dropdown-content {
  background-color: #002255;
}
.main {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  display: grid;
  display: flex;
  justify-content: space-between;
  padding-top: 50px;
}
.employee-card {
  width: 220px;
  background: #fff;
  padding: 15px;
  margin: 20px auto;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  text-align: center;
  cursor: pointer;
  margin-left: 50px;
  margin-bottom: 150px;
}
.employee-card img {
  width: 100px;
  border-radius: 50%;
}
.employee-card h3 {
  margin: 10px 0 5px;
}
.employee-card .stars i {
  color: gold;
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  position: relative;
  z-index: 10000;
}
.modal-content img {
  width: 120px;
  border-radius: 50%;
  display: block;
  margin: 0 auto 15px;
}
.modal-content .close {
  position: absolute;
  top: 10px; right: 20px;
  font-size: 28px;
  cursor: pointer;
}
.star-rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}
.star-rating input[type="radio"] {
  display: none;
}
.star-rating label {
  font-size: 24px;
  color: #ccc;
  cursor: pointer;
}
.star-rating input[type="radio"]:checked ~ label {
  color: gold;
}
.comment-box {
  margin-top: 15px;
}
.comment-box textarea {
  width: 100%;
  border-radius: 6px;
  padding: 10px;
  resize: none;
}
.modal-content button {
  margin-top: 15px;
  background: #002966;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
}
h1{
  margin-top: 50px;
}
.search-wrapper {
    position: relative;
    width: 1000px;
    margin: 0 auto;
}
.search-wrapper input {
    width: 100%;
    padding: 10px 40px 10px 40px;
    border-radius: 25px;
    border: 1px solid #ccc;
    outline: none;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}
.search-wrapper .search-icon {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #888;
    font-size: 16px;
}
</style>
  </head>
  <link rel="stylesheet" href="Dashboard.css" />
  <body>
    <nav class="sidebar">
            <h2 id="h2">HR1</h2>
      <div class="menu-content">
        <ul class="menu-items">
            
            <ul class="menu-items submenu">
              <div class="menu-title">
              <i class='bx bx-arrow-back'></i>Back</div>
              <li class="item1">
                <a>EMPLOYEE MANAGEMENT</a>
                <div class="dropdown-content">
                  <a href="employee_database.php"><small>EMPLOYEE DATABASE</small></a>
                  <a href="performance_and_appraisals.php"><small>PERFORMANCE & APPRAISALS</small></a>
                </div>
              </li>
              <li class="item1">
                <a>RECRUITMENT</a>
                <div class="dropdown-content">
                <a href="job_posting.php"><small>JOB POSTING</small></a>
                <a href="candidate_sourcing_&_tracking.php"><small>CANDIDATE SOURCING & TRACKING</small></a>
                <a href="interview_scheduling.php"><small>INTERVIEW SCHEDULING</small></a>
                <a href="assessment_&_screening.php"><small>ASSESSMENT & SCREENING</small></a>
                </div>
                </li>
              <li class="item1">
                <a>APPLICANT MANAGEMENT</a>
                <div class="dropdown-content">
                  <a href="#"><small>RESUME PARSING & STORAGE</small></a>
                  <a href="#"><small>COMMUNICATION & NOTIFICATIONS</small></a>
                  <a href="#"><small>DOCUMENT MANAGEMENT</small></a>
                </div>
              </li>
              <li class="item1">
                <a>NEW HIRED ONBOARDING SYSTEM</a>
                <div class="dropdown-content">
                  <a href="#"><small>DIGITAL ONBOARDING PROCESS</small></a>
                  <a href="#"><small>WELCOME KIT & ORIENTATION</small></a>
                  <a href="#"><small>USER ACCOUNT & SETUP</small></a>
                </div>
              </li>
              <li class="item1">
                <a>RECRUITING ANALYTIC & REPORTING</a>
                <div class="dropdown-content">
                  <a href="#"><small>HIRING METRICS DASHBOARD</small></a>
                  <a href="#"><small>RECRUITMENT FUNNEL & ANALYSIS</small></a>
                  <a href="#"><small>RECRUITER PERFORMANCE TRACKING</small></a>
                  <a href="#"><small>DIVERSITY & COMPLIANCE REPORT</small></a>
                  <a href="#"><small>COST & BUDGET ANALYSIS</small></a>
                </div>
              </li>
            </ul>
          </li>
            
        </ul>
      </div>
    </nav>
    <nav class="navbar">
      <i class="fa-solid fa-bars" id="sidebar-close"></i>
    </nav>
    <main class="main">
    <h1>Performance & Appraisals</h1>
    <div class="employee-container">
<div class="search-wrapper">
  <i class="fa fa-search search-icon"></i>
  <input type="text" id="searchBar" placeholder="Search employees...">
</div>
      <div class="employee-card" onclick="openModal()">
        <img src="mark.jpg" alt="Employee Photo">
        <h3>Siegfried Mar Viloria</h3>
        <p>ID Number: 001</p>
        <p>Job Position: Team Leader/ Developer</p>
        <div class="stars">
          <i class="fa fa-star"></i><i class="fa fa-star"></i>
          <i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
          <i class="fa fa-star-o"></i>
        </div>
      </div>
      
      <div class="employee-card" onclick="openModal()">
        <img src="lloyd.jpg" alt="Employee Photo">
        <h3>John Lloyd Morales</h3>
        <p>ID Number: 002</p>
        <p>Job Position: System Analyst</p>
        <div class="stars">
          <i class="fa fa-star"></i><i class="fa fa-star"></i>
          <i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
          <i class="fa fa-star-o"></i>
        </div>
      </div>

      <div class="employee-card" onclick="openModal()">
        <img src="mark.jpg" alt="Employee Photo">
        <h3>Andy Ferrer</h3>
        <p>ID Number: 003</p>
        <p>Job Position: Document Specialist</p>
        <div class="stars">
          <i class="fa fa-star"></i><i class="fa fa-star"></i>
          <i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
          <i class="fa fa-star-o"></i>
        </div>
      </div>

      <div class="employee-card" onclick="openModal()">
        <img src="mark.jpg" alt="Employee Photo">
        <h3>Andrea Ilagan</h3>
        <p>ID Number: 004</p>
        <p>Job Position: Technical Support Analyst</p>
        <div class="stars">
          <i class="fa fa-star"></i><i class="fa fa-star"></i>
          <i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
          <i class="fa fa-star-o"></i>
        </div>
      </div>

      <div class="employee-card" onclick="openModal()">
        <img src="mark.jpg" alt="Employee Photo">
        <h3>Charlotte Achivida</h3>
        <p>ID Number: 005</p>
        <p>Job Position: Cyber Security Analyst</p>
        <div class="stars">
          <i class="fa fa-star"></i><i class="fa fa-star"></i>
          <i class="fa fa-star"></i><i class="fa fa-star-half-alt"></i>
          <i class="fa fa-star-o"></i>
        </div>
      </div>

      <!-- Modal -->
      <div id="employeeModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <img src="lloyd.jpg" alt="Employee Photo">
          <h2>Siegfried Mar Viloria</h2>
          <p><strong>ID:</strong> EMP001</p>
          <p><strong>Position:</strong>IT Support</p>
          <p><strong>Status:</strong> Active</p>

      
          <div class="rating">
            <label>Rate Employee:</label>
            <div class="star-rating">
              <input type="radio" name="stars" value="5" id="star5"><label for="star5">&#9733;</label>
              <input type="radio" name="stars" value="4" id="star4"><label for="star4">&#9733;</label>
              <input type="radio" name="stars" value="3" id="star3"><label for="star3">&#9733;</label>
              <input type="radio" name="stars" value="2" id="star2"><label for="star2">&#9733;</label>
              <input type="radio" name="stars" value="1" id="star1"><label for="star1">&#9733;</label>
            </div>
          </div>
      
          <div class="comment-box">
            <label>Add Comment:</label>
            <textarea rows="3" placeholder="Write your comment here..."></textarea>
          </div>
      
          <button onclick="submitFeedback()">Submit</button>
        </div>
      </div>

      <div id="employeeModal" class="modal">
        <div class="modal-content">
          <span class="close" onclick="closeModal()">&times;</span>
          <img src="image/lloyd.jpg" alt="Employee Photo">
          <h2>John Lloyd Morales</h2>
          <p><strong>ID:</strong> EMP002</p>
          <p><strong>Position:</strong>Junior Software Developer</p>
          <p><strong>Status:</strong> Active</p>
      
          <div class="rating">
            <label>Rate Employee:</label>
            <div class="star-rating">
              <input type="radio" name="stars" value="5" id="star5"><label for="star5">&#9733;</label>
              <input type="radio" name="stars" value="4" id="star4"><label for="star4">&#9733;</label>
              <input type="radio" name="stars" value="3" id="star3"><label for="star3">&#9733;</label>
              <input type="radio" name="stars" value="2" id="star2"><label for="star2">&#9733;</label>
              <input type="radio" name="stars" value="1" id="star1"><label for="star1">&#9733;</label>
            </div>
          </div>
      
          <div class="comment-box">
            <label>Add Comment:</label>
            <textarea rows="3" placeholder="Write your comment here..."></textarea>
          </div>
      
          <button onclick="submitFeedback()">Submit</button>
        </div>
      </div>
    </main>      
     <script src="Dashboard.js"></script>
     <script>
      function openModal() {
        document.getElementById("employeeModal").style.display = "flex";
      }
    
      function closeModal() {
        document.getElementById("employeeModal").style.display = "none";
      }
    
      function submitFeedback() {
        const rating = document.querySelector('input[name="stars"]:checked');
        const comment = document.querySelector('.comment-box textarea').value;
        if (rating) {
          alert(`Rated ${rating.value} star(s)\nComment: ${comment}`);
          closeModal();
        } else {
          alert("Please select a star rating.");
        }
      }
    
      // Optional: close modal on outside click
      window.onclick = function(event) {
        const modal = document.getElementById("employeeModal");
        if (event.target === modal) {
          modal.style.display = "none";
        }
      };
      const searchBar = document.getElementById("searchBar");

searchBar.addEventListener("input", function () {
  const query = searchBar.value.toLowerCase();
  const cards = document.querySelectorAll(".employee-card");

  cards.forEach(card => {
    const name = card.getAttribute("data-name").toLowerCase();
    if (name.includes(query)) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
});
  </script>
    
  </body>
</html>