function signin(phone, password) {

	if (!verifyPhone(phone)) {
		alert("휴대폰 번호를 정확하게 입력해주세요.");
		return;
	}
	if (!verifyPassword(password)) {
		alert("패스워드를 정확하게 입력해주세요.");
		return;
	}

	let path = "/account/signin";
	let params = [
		[ "phone",    phone ],
		[ "password", password ]
	];

	sendPostRequest(path, params, function(response) {

		let result = JSON.parse(response);
		if (result.status == 1) {
			location.href = "/account/info";
		} else if (result.status == 2) {
			alert("패스워드가 일치하지 않습니다.");
		} else if (result.status == 3) {
			alert("계정이 존재하지 않습니다.");
		}

	});

}

function signup(phone, password, name, college, sex, email, major, birthday) {

	if (!verifyPhone(phone)) {
		alert("휴대폰 번호를 정확하게 입력해주세요.");
		return;
	}
	if (!verifyPassword(password)) {
		alert("패스워드를 정확하게 입력해주세요.");
		return;
	}

	let path = "/account/signup";
	let params = [
		[ "phone",    phone ],
		[ "password", password ],
		[ "name",     name ],
		[ "college",  college ],
		[ "sex",      sex ],
		[ "email",    email ],
		[ "major",    major ],
		[ "birthday", birthday ]
	];

	sendPostRequest(path, params, function(response) {

		let result = JSON.parse(response);
		if (result.status == 1) {
			alert("회원가입을 완료했습니다.<br>로그인 페이지로 이동합니다.")
			location.href = "/account/signin";
		} else if (result.status == 2) {
			alert("이미 가입된 계정입니다.");
		} else if (result.status == 3) {
			alert("모든 필드를 정확히 입력해주세요.");
		} else if (result.status == 4) {
			alert("서버 오류가 발생했습니다.");
		}

	});

}

function signout() {
	if (confirm("로그아웃하시겠습니까?")) {
		location.href = "/account/signout";
	}
}

// 휴대폰 번호의 유효성을 검증
function verifyPhone(phone) {
	return true;
}

// 패스워드의 유효성을 검증
function verifyPassword(password) {
	return true;
}