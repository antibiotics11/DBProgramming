// 공모전 등록요청 전송
function createContest(
  title, field, headcount,        // 제목, 모집 분야, 모집인원
  beginningdate, deadline,        // 모집 시작일, 종료 기한
  intramural, rating, region      // 참여 교내외 조건, 참여 조건 점수, 참여 조건 지역
) {

  if (!checkValidationFields(title, headcount, beginningdate, deadline)) {
    return;
  }

  beginningdate = (toTimestamp(beginningdate) / 1000).toString();
  deadline = (toTimestamp(deadline) / 1000).toString();

  let path = "/contest/create";
  let params = [
    [ "title",         title ],
    [ "field",         field ],
    [ "headcount",     headcount ],
    [ "beginningdate", beginningdate ],
    [ "deadline",      deadline ],
    [ "intramural",    intramural ],
    [ "rating",        rating ],
    [ "region",        region ]
  ];

  sendPostRequest(path, params, function(response) {

    let result = JSON.parse(response);
    if (result.status == 1) {
      location.href = "/contest/v/" + result.code;
    } else if (result.status == 2) {
      alert("모든 필드를 정확하게 입력해주세요.")
    } else {
      alert("서버 오류가 발생했습니다.");
    }

  });

}

// 공모전 수정요청 전송
function updateContest(
  // 수정 가능 항목: 인원수, 종료일, 평가점수, 지역
  code, title, headcount, beginningdate, deadline, rating, region
) {

  if (!checkValidationFields(title, headcount, deadline, deadline)) {
    return;
  }

  deadline = (toTimestamp(deadline) / 1000).toString();

  let path = "/contest/update";
  let params = [
    [ "code",      code ],
    [ "headcount", headcount ],
    [ "deadline",  deadline ],
    [ "rating",    rating ],
    [ "region",    region ]
  ];

  sendPostRequest(path, params, function(response) {

    let result = JSON.parse(response);
    if (result.status == 1) {
      alert("수정했습니다.");
      location.reload();
    } else {
      alert("수정할수 없습니다.");
    }

  });

}

// 공모전 모집종료요청 전송
function closeContest(code) {

  if (!confirm("이 공모전을 모집 종료하시겠습니까?")) {
    return;
  }

  let path = "/contest/close";
  let params = [ [ "code", code ] ];

  sendPostRequest(path, params, function(response) {

    let result = JSON.parse(response);
    if (result.status == 1) {
      alert("모집이 종료되었습니다.");
      location.reload();
    } else {
      alert("종료할수 없습니다.");
    }

  })

}

// 공모전 삭제요청 전송
function deleteContest(code) {      // 삭제할 공모전 코드

  if (!confirm("정말 삭제하시겠습니까?")) {
    return;
  }

  let path = "/contest/delete";
  let params = [ [ "code", code ] ];

  sendPostRequest(path, params, function(response) {

    let result = JSON.parse(response);
    if (result.status == 1) {
      alert("삭제했습니다.");
      location.href = "/contest/list";
    } else {
      alert("삭제할수 없습니다.");
    }

  });

}

// 공모전 참가요청 전송
function applyForContest(code) {

  let path = "/contest/apply";
  let params = [ [ "code", code ] ];

  sendPostRequest(path, params, function(response) {

    let result = JSON.parse(response);
    switch (result.status) {

    case 1 :
      alert(parseInt(result.apply) ? "참여 신청했습니다." : "참여 신청을 취소했습니다.");
      location.reload();
      break;

    case 2 :
      alert("본인이 등록한 공모전에는 신청할 수 없습니다."); break;

    case 3 :
      alert("일치하는 공모전이 없습니다."); break;

    case 4 :
      alert("서버 오류가 발생했습니다.\r\n관리자에게 문의해주세요."); break;

    case 11 :
      alert("아직 모집이 시작되지 않았습니다."); break;

    case 12 :
      alert("이미 모집이 종료되었습니다."); break;

    case 13 :
      alert("지역 조건이 일치하지 않아 신청할 수 없습니다."); break;

    case 14 :
      alert("학교 조건이 일치하지 않아 신청할 수 없습니다."); break;

    case 15 :
      alert("현재 등급으로는 신청할 수 없습니다."); break;

    };

  });

}

// 참가자 목록을 출력
function viewApplicants(code) {
  location.href = "/contest/v/a/" + code;
}

function checkValidationFields(title, headcount, beginningdate, deadline) {

  if (!validateTitle(title)) {
    alert("제목은 최소 10글자 이상 입력해주세요.");
    return false;
  }
  if (!validateHeadcount(headcount)) {
    alert("인원은 최소 1명이 있어야합니다.");
    return false;
  }
  if (!validateTime(beginningdate) || !validateTime(deadline)) {
    alert("날짜는 yyyymmdd 형식으로 입력해주세요.");
    return false;
  }
  if (toTimestamp(deadline) <= Date.now()) {
    alert("마감일은 오늘 이후 날짜로 지정되어야 합니다.");
    return false;
  }
  return true;

}

function validateTitle(title) {
  return (title.length >= 10);
}

function validateHeadcount(headcount) {
  return (headcount > 0 && headcount <= 500);
}

function validateTime(strTime) {
  return (strTime.length == 8);
}

function toTimestamp(intTime) {

  let dmy = new Array();
  let strTime = "";
  let timestamp = 0;

  for (let i = 0; i < 2; i++) {
    dmy[i] = intTime % 100;
    intTime = Math.round(intTime / 100);
  }
  dmy[2] = intTime;

  strTime = dmy[2] + "-" + dmy[1] + "-" + dmy[0];
  timestamp = Date.parse(strTime);

  return timestamp;

}
