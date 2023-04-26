<?php

namespace ContestApp\Resource;

enum MimeType: String {

	case _AAC    = "audio/aac";
	case _ABW    = "application/x-abiword";
	case _ARC    = "application/x-freearc";
	case _AVIF   = "image/avif";
	case _AVI    = "video/x-msvideo";
	case _AZW    = "application/vnd.amazon.ebook";
	case _BIN    = "application/octet-stream";
	case _BMP    = "image/bmp";
	case _BZ     = "application/x-bzip";
	case _BZ2    = "application/x-bzip2";
	case _CDA    = "application/x-cdf";
	case _CSH    = "application/x-csh";
	case _CSS    = "text/css";
	case _CSV    = "text/csv";
	case _DOC    = "application/msword";
	case _DOCX   = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
	case _EOT    = "application/vnd.ms-fontobject";
	case _EPUB   = "application/epub+zip";
	case _GZ     = "application/gzip";
	case _GIF    = "image/gif";
	case _HTM    = "text/html ";
	case _HTML   = "text/html";
	case _ICO    = "image/vnd.microsoft.icon";
	case _ICS    = "text/calendar";
	case _JAR    = "application/java-archive";
	case _JPG    = "image/jpeg";
	case _JPEG   = "image/jpeg ";
	case _JS     = "text/javascript";
	case _JSON   = "application/json";
	case _JSONLD = "application/ld+json";
	case _MID    = "audio/midi";
	case _MIDI   = "audio/x-midi";
	case _MJS    = "text/javascript ";
	case _MP3    = "audio/mpeg";
	case _MP4    = "video/mp4";
	case _MPEG   = "video/mpeg";
	case _MPKG   = "application/vnd.apple.installer+xml";
	case _ODP    = "application/vnd.oasis.opendocument.presentation";
	case _ODS    = "application/vnd.oasis.opendocument.spreadsheet";
	case _ODT    = "application/vnd.oasis.opendocument.text";
	case _OGA    = "audio/ogg";
	case _OGV    = "video/ogg";
	case _OGX    = "application/ogg";
	case _OPUS   = "audio/opus";
	case _OTF    = "font/otf";
	case _PNG    = "image/png";
	case _PDF    = "application/pdf";
	case _PPT    = "application/vnd.ms-powerpoint";
	case _PPTX   = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
	case _RAR    = "application/vnd.rar";
	case _RTF    = "application/rtf";
	case _SH     = "application/x-sh";
	case _SVG    = "image/svg+xml";
	case _TIF    = "image/tiff ";
	case _TAR    = "application/x-tar";
	case _TIFF   = "image/tiff";
	case _TS     = "video/mp2t";
	case _TTF    = "font/ttf";
	case _TXT    = "text/plain";
	case _VSD    = "application/vnd.visio";
	case _WAV    = "audio/wav";
	case _WEBA   = "audio/webm ";
	case _WEBM   = "audio/webm";
	case _WEBP   = "image/webp";
	case _WOFF   = "font/woff";
	case _WOFF2  = "font/woff2";
	case _XHTML  = "application/xhtml+xml";
	case _XLS    = "application/vnd.ms-excel";
	case _XLSX   = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
	case _XML    = "application/xml";
	case _XUL    = "application/vnd.mozilla.xul+xml";
	case _ZIP    = "application/zip";
	case _3GP    = "video/3gpp";
	case _3G2    = "video/2gpp2";
	case _7Z     = "application/x-7z-compressed";

	public static function fromName(String $name): ?Object {
	
		try {
			return constant(sprintf("self::_%s", strtoupper(trim($name))));
		} catch (\Throwable $e) {
			return null;
		}
		
	}

};
