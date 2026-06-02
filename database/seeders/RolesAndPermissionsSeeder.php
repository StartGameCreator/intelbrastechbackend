namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa o cache de permissões do Spatie antes de rodar
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. CRIAÇÃO DE PERMISSÕES CRÍTICAS DO ECOSSISTEMA
        Permission::create(['name' => 'approve users']);
        Permission::create(['name' => 'publish services']);
        Permission::create(['name' => 'submit proposals']);
        Permission::create(['name' => 'moderate social network']);
        Permission::create(['name' => 'access regional intelligence']);

        // 2. CRIAÇÃO DOS PAPÉIS (ROLES) E ATRIBUIÇÃO DE PERMISSÕES
        
        // Master / Admin Geral - Tem poder total
        $masterRole = Role::create(['name' => 'master']);
        $masterRole->givePermissionTo(Permission::all());

        // Regional - Moderação local e inteligência de mapa
        $regionalRole = Role::create(['name' => 'regional']);
        $regionalRole->givePermissionTo(['approve users', 'access regional intelligence', 'moderate social network']);

        // Técnico - Envia propostas de instalação/manutenção
        $technicianRole = Role::create(['name' => 'technician']);
        $technicianRole->givePermissionTo(['submit proposals']);

        // Empresa / Integrador / Distribuidor
        $companyRole = Role::create(['name' => 'company']);
        $companyRole->givePermissionTo(['publish services']);

        // Cliente Comum B2C
        $clientRole = Role::create(['name' => 'client']);
        $clientRole->givePermissionTo(['publish services']);
    }
}