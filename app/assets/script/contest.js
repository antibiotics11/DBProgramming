function create(
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