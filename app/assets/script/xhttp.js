/**
 * 서버로 비동기 POST 요청을 보낸다
 *
 * @param {string}  path     요청을 보낼 경로
 * @param {array}   params   post body에 담을 데이터 배열
 * @param {closure} handler  서버로부터 받은 응답을 처리할 클로저
 */
function sendPostRequest(path, params, handler) {

  let xhttp = new XMLHttpRequest();
  let body = setParams(params);

  xhttp.open("POST", path, true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      handler(this.responseText);
    }
  }
  xhttp.send(body);

}

function setParams(values, urlEncode = true) {

  let params = "";
  let paramValue = "";

  for (let i = 0; i < values.length; i++) {
    paramValue = urlEncode ? encodeURIComponent(values[i][1]) : values[i][1];
    params += (values[i][0] + "=" + paramValue);
    if (i != values.length - 1) {
      params += "&";
    }
  }

  return params;

}
