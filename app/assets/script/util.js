/**
 * 쿠키 이름으로 쿠키 값을 읽어온다
 *
 * @param {string} name  쿠키 이름
 * @returns  쿠키 값
 */
function getCookieByName(name) {

 	let value = `; ${document.cookie}`;
	let parts = value.split(`; ${name}=`);

	if (parts.length === 2) {
		return parts.pop().split(';').shift();
	}

}

/**
 * 문자열을 클립보드에 복사한다
 *
 * @param {string} value    복사할 문자열
 * @param {string} message  복사 후 출력할 메시지
 */
function copyToClipboard(value = "", message = "", alert = true) {

	let tmp = document.createElement("textarea");

	document.body.appendChild(tmp);
	tmp.value = value;

	tmp.select();
	document.execCommand("copy");
	document.body.removeChild(tmp);

	if (message.length > 0 && alert) {
		alert(message);
	}

}
