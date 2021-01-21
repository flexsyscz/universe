<?php

// The Nette Tester command-line runner can be
// invoked through the command: ../vendor/bin/tester .

declare(strict_types=1);

setlocale(LC_ALL, 'cs_CZ.utf8');

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer install`';
	exit(1);
}


// configure environment
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');


function getTempDir(): string
{
	return __DIR__ . '/tmp';
}


function getLogDir(): string
{
	return __DIR__ . '/log';
}


function before(Closure $function = null)
{
	static $val;
	if (!func_num_args()) {
		return $val ? $val() : null;
	}
	$val = $function;
}


function after(Closure $function = null)
{
	static $val;
	if (!func_num_args()) {
		return $val ? $val() : null;
	}
	$val = $function;
}


function test(string $title, Closure $function): void
{
	before();
	$function();
	after();
}