<?php

namespace ContestApp\System;

class Time {

	public static function setTimeZone(String $timezone = "UTC"): String {

		date_default_timezone_set($timezone);
		return date_default_timezone_get();

	}

	public static function DateYMD(String $separator = "-", int $timestamp = -1): String {

		$timestamp = ($timestamp == -1) ? time() : $timestamp;
		$ymd = date(sprintf("Y%sm%sd", $separator, $separator), $timestamp);

		return $ymd;

	}

	public static function DateRFC2822(int $timestamp = -1): String {

		$timestamp = ($timestamp == -1) ? time() : $timestamp;
		$rfc2822 = sprintf("%s%s", substr(date(DATE_RFC2822, $timestamp), 0, -5), date("T"));

		return $rfc2822;

	}

	public static function StrTimestampToInt(String $timestamp): int {

		list($date, $time) = explode(" ", $timestamp);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $minute, $second) = explode(":", $time);

		return mktime($hour, $minute, $second, $month, $day, $year);

	}

};
