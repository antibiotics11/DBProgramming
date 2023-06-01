function signin(phone, password) {

	if (!verifyPhone(phone)) {
		alert("휴대폰 번호를 정확하게 입력해주세요.");
		return;
	}
	if (!verifyPassword(password)) {
		alert("패스워드는 4자리 이상 입력해주세요.");
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
		alert("패스워드는 4자리 이상 입력해주세요.");
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
			alert("회원가입을 완료했습니다.\r\n로그인 페이지로 이동합니다.")
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

function rateMember(targetPhone, like = 1) {

	let path = "/account/r/" + targetPhone;
	let params = [
		[ "target", targetPhone ],
		[ "like",   like ]
	];

	sendPostRequest(path, params, function(response) {

		let result = JSON.parse(response);
		if (result.status == 1) {
			location.reload();
		} else if (result.status == 2) {
			alert("이미 평가한 회원입니다.");
		} else if (result.status == 3) {
			alert("계정 정보가 없습니다.");
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

function deleteAccount() {
	if (confirm("모든 데이터가 삭제되며 복구할 수 없습니다.\r\n정말 탈퇴하시겠습니까?")) {
		location.href = "/account/delete";
	}
}

// 휴대폰 번호에서 숫자가 아닌것을 모두 제거
function trimPhone(phone) {
	return phone.replace(/\D/g, "");
}

// 휴대폰 번호가 11자리 숫자인지 확인 (01000000000 형식)
function verifyPhone(phone) {
	return (trimPhone(phone).length == 11);
}

// 패스워드의 유효성을 검증
function verifyPassword(password) {
	return (password.length >= 4);
}
