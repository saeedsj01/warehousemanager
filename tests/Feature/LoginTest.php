<?php
namespace Tests\Feature;
use Tests\TestCase;
class LoginTest extends TestCase { public function test_login_page_is_available(): void { $this->get('/login')->assertOk(); } }
