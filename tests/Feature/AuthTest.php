<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_split_screen_layout(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('UNIVERSITAS LAMPUNG');
        $response->assertSee('Layanan administrasi akademik yang lebih mudah.');
        $response->assertSee('SELAMAT DATANG');
        $response->assertSee('Sistem Layanan Administrasi Mahasiswa');
        $response->assertSee('Login melalui SSO');
        $response->assertSee('Fakultas Teknik Universitas Lampung');
    }

    public function test_sso_login_as_mahasiswa(): void
    {
        $mhs = User::create([
            'name' => 'Fajar Pratama',
            'email' => 'mahasiswa@silam.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_MAHASISWA,
            'npm' => '22081010001',
        ]);

        $response = $this->post('/login', [
            'role' => 'mahasiswa',
            'akun_kampus' => '22081010001',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($mhs);
    }

    public function test_sso_login_as_petugas(): void
    {
        $petugas = User::create([
            'name' => 'Budi Santoso',
            'email' => 'petugas@silam.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_PETUGAS,
        ]);

        $response = $this->post('/login', [
            'role' => 'petugas',
            'akun_kampus' => 'petugas@silam.ac.id',
        ]);

        $response->assertRedirect('/petugas');
        $this->assertAuthenticatedAs($petugas);
    }

    public function test_sso_login_as_admin(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@silam.ac.id',
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'role' => 'admin',
            'akun_kampus' => 'admin@silam.ac.id',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_credential_login_with_password(): void
    {
        $user = User::create([
            'name' => 'Fajar Pratama',
            'email' => 'mahasiswa@silam.ac.id',
            'password' => bcrypt('secret123'),
            'role' => User::ROLE_MAHASISWA,
            'npm' => '22081010001',
        ]);

        $response = $this->post('/login', [
            'email' => 'mahasiswa@silam.ac.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
