<?php
namespace App\Console\Commands;

use App\Modules\Menu\Models\Menu;
use Illuminate\Console\Command;

/**
 * Sekali-jalan: buat baris menu + privilege modul Pengumuman (app/Modules/Pengumuman).
 * Akses default hanya Super Admin; role lain diatur lewat menu Privilege.
 * Aman dijalankan berkali-kali (idempotent).
 */
class SetupPengumumanMenu extends Command
{
    protected $signature = 'pengumuman:setup-menu';

    protected $description = 'Buat menu + privilege modul Pengumuman (akses default: Super Admin).';

    public function handle()
    {
        $module = 'pengumuman';

        if (Menu::where('module', $module)->exists()) {
            $this->info("Menu '{$module}' sudah ada, tidak ada yang diubah.");
            return self::SUCCESS;
        }

        // createByModule() mengambil parent_id dari config laralag.dict.menu_referensi yang bisa kosong
        // (kolom menu.parent_id NOT NULL). Taruh satu grup dengan menu Izin Tidak Hadir.
        $parentId = config('laralag.dict.menu_referensi')
            ?: Menu::where('module', 'ijin')->value('parent_id');
        if (! $parentId) {
            $this->error("Tidak menemukan parent menu (menu 'ijin' tidak ada). Buat menu manual lewat menu Menu.");
            return self::FAILURE;
        }
        config(['laralag.dict.menu_referensi' => $parentId]);

        Menu::createByModule($module);

        $menu = Menu::where('module', $module)->first();
        $menu->icon = 'fa-bullhorn';
        $menu->save();

        $this->info("Menu '{$module}' dibuat. Akses penuh untuk Super Admin; atur role lain lewat menu Privilege, lalu login ulang.");
        return self::SUCCESS;
    }
}
