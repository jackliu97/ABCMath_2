<?php
namespace ABCMath\Meta\Implement;

interface HookInterface{
	public function setParameters($inParam);
	public function run();
}