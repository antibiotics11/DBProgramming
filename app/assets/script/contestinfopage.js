let creatorPhone = document.getElementById("phone").value.trim();
let memberPhone  = getCookieByName("phone").trim();

let done = parseInt(document.getElementById("done").value);

if (userIsCreator(creatorPhone, memberPhone)) {   // 현재 공모전 게시자면
  document.getElementById("view_applicants").style.display = "block";  // 지원자 버튼 출력
  if (done) {     // 모집종료된 공모전이면
    document.getElementById("submit_close").style.display  = "none";   // 모집종료 버튼 가리기
    document.getElementById("submit_update").style.display = "none";   // 수정 버튼 가리기
  } else {
    document.getElementById("submit_close").style.display  = "block";  // 모집종료 버튼 출력
    document.getElementById("submit_update").style.display = "block";  // 수정 버튼 출력
  }
  document.getElementById("submit_delete").style.display   = "block";  // 삭제 버튼 출력
  document.getElementById("submit_entry").style.display    = "none";   // 참가지원 버튼 가리기
} else {    // 게시자가 아니면
  document.getElementById("view_applicants").style.display = "none";   // 지원자 버튼 가리기
  document.getElementById("submit_close").style.display    = "none";   // 모집종료 버튼 가리기
  document.getElementById("submit_update").style.display   = "none";   // 수정 버튼 가리기
  document.getElementById("submit_delete").style.display   = "none";   // 삭제 버튼 가리기
  if (done) {
    document.getElementById("submit_entry").style.display  = "none";
  } else {
    document.getElementById("submit_entry").style.display  = "block";  // 참가지원 버튼 출력
  }
}

function userIsCreator(creatorPhone, memberPhone) {
  return (creatorPhone === memberPhone);
}

// 현재 공모전 코드를 확인한다.
function getContestCode() {
  let code = document.getElementById("code").value;   // code 요소에서 코드를 가져온다
  if (isNaN(code) || code.length < 1) {               // 가져온 값이 뭔가 이상하면
    code = window.location.pathname.split("/").pop(); // 현재 URL에서 코드를 가져온다
  }
  return code;
}

// 현재 폼을 수정 가능한 폼으로 변경한다.
function doUpdate() {

  let bgColor = "white";

  let headcountElement = document.getElementById("headcount");
  let deadlineElement = document.getElementById("deadline");
  let ratingElement = document.getElementById("rating");
  let regionElement = document.getElementById("region");

  headcountElement.readOnly = false;      // 인원수를 입력 가능하게
  headcountElement.className = "input_sign";

  deadlineElement.readOnly = false;       // 종료일을 입력 가능하게
  deadlineElement.className = "input_sign";

  ratingElement.disabled = false;         // 평가점수 선택 가능하게
  ratingElement.className = "input_sign";

  regionElement.disabled = false;         // 지역 선택 가능하게
  regionElement.className = "input_sign";

  if (document.getElementById("submit_update").value === "수정 확인") {
    submitUpdate();
  } else {
    document.getElementById("submit_update").value = "수정 확인";
  }

}

// 수정 요청을 보낸다
function submitUpdate() {
  updateContest(
    getContestCode(),
    document.getElementById("title").value,
    document.getElementById("headcount").value,
    document.getElementById("beginningdate").value,
    document.getElementById("deadline").value,
    document.getElementById("rating").value,
    document.getElementById("region").value
  );
}

// 모집종료 요청을 보낸다
function submitClose() {
  closeContest(getContestCode());
}

// 삭제 요청을 보낸다
function submitDelete() {
  deleteContest(getContestCode());
}

// 참가신청 요청을 보낸다
function submitApply() {
  applyForContest(getContestCode());
}
