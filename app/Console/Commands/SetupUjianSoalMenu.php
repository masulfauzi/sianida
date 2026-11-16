<?php
namespace App\Console\Commands;

use App\Modules\Menu\Models\Menu;
use App\Modules\Privilege\Models\Privilege;
use App\Modules\Role\Models\Role;
use Illuminate\Console\Command;

/**
 * Sekali-jalan: buat baris menu + privilege untuk modul Bank Soal Ujian
 * (app/Modules/UjianSoal), supaya guru tidak kena 403 dari middleware
 * AuthorizeRequest. Bukan migration - hanya mengisi data lewat Eloquent,
 * aman dijalankan berkali-kali (idempotent).
 */
class SetupUjianSoalMenu extends Command
{
    protected $signature = 'ujiansoal:setup-menu';

    protected $description = "Buat menu + privilege modul Bank Soal Ujian, dan beri akses penuh ke role Guru.";

    // UUID role "Guru" - dipakai konsisten di banyak controller lain (Jadwal, Jurnal, dst).
    // Kalau di environment Anda ternyata beda, jalankan dengan --role-id=<uuid milik Anda>.
    private const ROLE_GURU_ID = '9ec7541e-5a5e-4a3a-a255-6ffb46895f46';

    protected function configure()
    {
        $this->addOption('role-id', null, \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL, 'UUID role yang diberi akses penuh (default: UUID role Guru yang dipakai di seluruh aplikasi)');
    }

    public function handle()
    {
        $module  = 'ujiansoal';
        $roleId  = $this->option('role-id') ?: self::ROLE_GURU_ID;

        $menu = Menu::where('module', $module)->first();

        if (! $menu) {
            Menu::createByModule($module);
            $menu = Menu::where('module', $module)->first();
            $this->info("Menu '{$module}' dibuat (akses penuh otomatis ke role Super Admin, 0 ke role lain).");
        } else {
            $this->info("Menu '{$module}' sudah ada, lanjut cek privilege.");
        }

        if (! $menu) {
            $this->error("Gagal membuat/menemukan menu '{$module}'.");
            return self::FAILURE;
        }

        $this->setupMonitoring($menu);

        $role = Role::find($roleId);
        if (! $role) {
            $this->warn("Role dengan id {$roleId} tidak ditemukan di tabel role. Privilege TIDAK diatur untuk role ini - atur manual lewat menu Privilege, atau ulangi dengan --role-id=<uuid yang benar>.");
            return self::SUCCESS;
        }

        $priv = Privilege::where('id_menu', $menu->id)->where('id_role', $role->id)->first();
        if (! $priv) {
            $priv          = new Privilege();
            $priv->id_menu = $menu->id;
            $priv->id_role = $role->id;
        }

        $priv->create    = 1;
        $priv->read      = 1;
        $priv->show      = 1;
        $priv->update    = 1;
        $priv->delete    = 1;
        $priv->show_menu = 1;
        $priv->save();

        $this->info("Privilege role '{$role->role}' untuk modul '{$module}' diset penuh (create/read/show/update/delete/show_menu).");
        $this->comment("Catatan: posisi menu di sidebar mengikuti pengaturan default Menu::createByModule() (parent_id dari config('laralag.dict.menu_referensi')). Atur ulang lewat menu Menu > Edit kalau mau dikelompokkan rapi - ini tidak memengaruhi hak akses, cuma tampilan sidebar.");

        return self::SUCCESS;
    }

    /**
     * Monitoring Bank Soal Ujian (route ujiansoal.monitoring.index).
     *
     * Hak akses route ini mengikuti menu "ujiansoal" (AuthorizeRequest membaca segmen pertama
     * nama route), jadi siapa pun yang punya read di "ujiansoal" - termasuk Guru - bisa membukanya.
     * Di sini: role Admin diberi read di "ujiansoal" (tanpa show_menu, supaya menu Bank Soal milik
     * guru tidak muncul di sidebar admin), dan menu sidebar terpisah "ujiansoalmonitoring" yang
     * menunjuk ke route monitoring ditampilkan untuk Admin.
     */
    private function setupMonitoring(Menu $menuUjianSoal)
    {
        $module  = 'ujiansoalmonitoring';
        $routing = 'ujiansoal.monitoring.index';

        $menu = Menu::where('module', $module)->first();
        if (! $menu) {
            // createByModule() mengambil parent_id dari config laralag.dict.menu_referensi yang bisa kosong;
            // taruh menu monitoring satu grup dengan menu Bank Soal Ujian.
            config(['laralag.dict.menu_referensi' => $menuUjianSoal->parent_id]);
            Menu::createByModule($module);
            $menu = Menu::where('module', $module)->first();
            $this->info("Menu sidebar '{$module}' dibuat.");
        }
        $menu->menu    = 'Monitoring Bank Soal Ujian';
        $menu->routing = $routing;
        $menu->save();
        $this->info("Menu sidebar '{$module}' menunjuk ke route '{$routing}'.");

        $admin = Role::where('role', 'Admin')->first();
        if (! $admin) {
            $this->warn("Role 'Admin' tidak ditemukan. Atur privilege monitoring manual lewat menu Privilege.");
            return;
        }

        // Tampilkan menu monitoring di sidebar admin
        $privMenu = $this->privilegeFor($menu, $admin);
        $privMenu->read      = 1;
        $privMenu->show      = 1;
        $privMenu->show_menu = 1;
        $privMenu->save();

        // Izin membuka route ujiansoal.monitoring.index
        $privAkses = $this->privilegeFor($menuUjianSoal, $admin);
        $privAkses->read = 1;
        $privAkses->save();

        $this->info("Role 'Admin' bisa membuka monitoring (read di 'ujiansoal', menu '{$module}' tampil di sidebar).");
    }

    private function privilegeFor(Menu $menu, Role $role)
    {
        $priv = Privilege::where('id_menu', $menu->id)->where('id_role', $role->id)->first();
        if (! $priv) {
            $priv            = new Privilege();
            $priv->id_menu   = $menu->id;
            $priv->id_role   = $role->id;
            $priv->create    = 0;
            $priv->read      = 0;
            $priv->show      = 0;
            $priv->update    = 0;
            $priv->delete    = 0;
            $priv->show_menu = 0;
        }

        return $priv;
    }
}
