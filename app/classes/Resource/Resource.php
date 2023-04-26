<?php

namespace ContestApp\Resource;

class Resource {

	protected Object $type;
	protected String $contents;

	public function __construct(?Object $type = null, String $contents = "") {

		if ($type == null) {
			$this->type = MimeType::_TXT;
		}
		$this->contents = $contents;

	}

	public function setMimeType(Object $type): void {

		if ($type instanceof MimeType) {
			$this->type = $type;
		} else {
			throw new \InvalidArgumentException(
				sprintf("%s is not MimeType object.", $type)
			);
		}

	}

	public function getMimeType(): Object {
		return $this->type;
	}

	public function setContents(String $contents): void {
		$this->contents = $contents;
	}

	public function getContents(): String {
		return $this->contents;
	}

};
