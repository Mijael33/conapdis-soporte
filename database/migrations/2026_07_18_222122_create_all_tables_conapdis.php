<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ====================================================================
        // BLOQUE 1: ESTRUCTURA GEOGRÁFICA (24 Estados de Venezuela)
        // ====================================================================
        Schema::create('estados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('region', 50)->comment('Región: Capital, Central, Occidental, Oriental, Los Llanos, Guayana');
            $table->unique('nombre', 'estados_nombre_unique');
            $table->index('region', 'estados_region_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE estados IS '24 estados de Venezuela con presencia institucional CONAPDIS'");

        Schema::create('sedes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('estado_id')->constrained('estados')->onDelete('restrict')->onUpdate('cascade');
            $table->string('nombre_sede', 150);
            $table->text('direccion');
            $table->string('codigo_postal', 10)->nullable();
            $table->index(['estado_id', 'nombre_sede'], 'sedes_estado_nombre_index');
            $table->unique(['estado_id', 'nombre_sede'], 'sedes_estado_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE sedes IS 'Sedes físicas de CONAPDIS agrupadas por estado'");

        Schema::create('departamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('restrict')->onUpdate('cascade');
            $table->string('nombre_departamento', 150);
            $table->integer('piso')->nullable();
            $table->string('extension_telefonica', 10)->nullable();
            $table->index(['sede_id', 'nombre_departamento'], 'deptos_sede_nombre_index');
            $table->unique(['sede_id', 'nombre_departamento'], 'deptos_sede_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE departamentos IS 'Departamentos y oficinas de cada sede CONAPDIS'");

        // ====================================================================
        // BLOQUE 2: USUARIOS (Con estado_id y sede_id)
        // ====================================================================
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->rememberToken();
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE users IS 'Usuarios del sistema con ubicación geográfica asignada'");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ====================================================================
        // BLOQUE 3: CATÁLOGOS DE TECNOLOGÍA
        // ====================================================================
        Schema::create('tipos_equipos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->unique('nombre', 'tipos_equipos_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE tipos_equipos IS 'Catálogo dinámico de tipos de equipos tecnológicos'");

        Schema::create('categorias_componentes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->unique('nombre', 'categorias_componentes_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE categorias_componentes IS 'Catálogo dinámico de categorías de componentes tecnológicos'");

        // ====================================================================
        // BLOQUE 4: TECNOLOGÍA - COMPONENTES Y EQUIPOS
        // ====================================================================
        Schema::create('componentes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('categoria_componente_id')->constrained('categorias_componentes')->onDelete('restrict')->onUpdate('cascade');
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->string('serial_unico', 150);
            $table->unique('serial_unico', 'componentes_serial_unique');
            $table->index('serial_unico', 'componentes_serial_index');
            $table->enum('estatus', ['Disponible', 'Instalado', 'En Revisión', 'Desincorporado'])->default('Disponible');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->index('estatus', 'componentes_estatus_index');
            $table->jsonb('caracteristicas_tecnicas')->nullable();
            $table->index('caracteristicas_tecnicas', 'componentes_caracteristicas_gin')->algorithm('gin');
            $table->text('observaciones')->nullable();
            $table->index(['categoria_componente_id', 'estatus'], 'componentes_categoria_estatus_index');
            $table->index(['sede_id', 'estatus'], 'componentes_sede_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE componentes IS 'Componentes tecnológicos con trazabilidad'");

        Schema::create('equipos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_inventario_institucional', 50);
            $table->unique('codigo_inventario_institucional', 'equipos_codigo_inv_unique');
            $table->index('codigo_inventario_institucional', 'equipos_codigo_inv_index');
            $table->string('serial_chasis', 150)->nullable();
            $table->foreignId('tipo_equipo_id')->constrained('tipos_equipos')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict')->onUpdate('cascade');
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->enum('estatus_general', ['Operativo', 'En Mantenimiento', 'Inoperativo', 'Donado/Desincorporado'])->default('Operativo');
            $table->index('estatus_general', 'equipos_estatus_index');
            $table->string('usuario_asignado_nombre', 150)->nullable();
            $table->string('usuario_asignado_cedula', 20)->nullable();
            $table->string('usuario_asignado_cargo', 150)->nullable();
            $table->index(['departamento_id', 'estatus_general'], 'equipos_depto_estatus_index');
            $table->index(['tipo_equipo_id', 'estatus_general'], 'equipos_tipo_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE equipos IS 'Equipos tecnológicos con código de inventario'");

        Schema::create('equipo_sistemas_operativos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nombre', 100);
            $table->string('arquitectura', 20)->nullable();
            $table->text('password_encriptada')->nullable();
            $table->boolean('tiene_contrasena')->default(false);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE equipo_sistemas_operativos IS 'Sistemas operativos con contraseñas cifradas'");

        Schema::create('bitacora_acceso_contrasenas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipo_so_id')->constrained('equipo_sistemas_operativos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->string('motivo', 200)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('fecha_acceso')->useCurrent();
        });
        DB::statement("COMMENT ON TABLE bitacora_acceso_contrasenas IS 'Registro de accesos a contraseñas de SO'");

        Schema::create('equipo_componente', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('componente_id')->constrained('componentes')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamp('fecha_instalacion')->useCurrent();
            $table->timestamp('fecha_desinstalacion')->nullable();
            $table->boolean('activo')->default(true);
            $table->index(['equipo_id', 'activo'], 'equipo_comp_equipo_activo_index');
            $table->index(['componente_id', 'activo'], 'equipo_comp_componente_activo_index');
            $table->timestamps();
        });
        DB::statement("CREATE UNIQUE INDEX equipo_componente_componente_activo_unique ON equipo_componente (componente_id) WHERE activo = true");
        DB::statement("COMMENT ON TABLE equipo_componente IS 'Historial de componentes instalados en equipos'");

        // ====================================================================
        // BLOQUE 5: ÓRDENES DE SERVICIO
        // ====================================================================
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_ticket', 30);
            $table->unique('codigo_ticket', 'ordenes_ticket_unique');
            $table->index('codigo_ticket', 'ordenes_ticket_index');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('sede_origen_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->text('problema_reportado_usuario');
            $table->text('diagnostico_tecnico')->nullable();
            $table->text('acciones_realizadas')->nullable();
            $table->enum('estatus_final', ['Reparado', 'En Espera de Repuesto', 'Remitido a Sede Central', 'Irrecuperable'])->nullable();
            $table->index('estatus_final', 'ordenes_estatus_index');
            $table->timestamp('fecha_inicio')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();
            $table->index(['equipo_id', 'estatus_final'], 'ordenes_equipo_estatus_index');
            $table->index(['sede_origen_id', 'estatus_final'], 'ordenes_sede_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE ordenes_servicio IS 'Órdenes de servicio técnico - Los estados crean tickets, Dtto Capital repara'");

        // ====================================================================
        // BLOQUE 6: CATÁLOGOS DE BIENES NACIONALES
        // ====================================================================
        Schema::create('categorias_bienes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->unique('nombre', 'categorias_bienes_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE categorias_bienes IS 'Catálogo de bienes nacionales: Escritorios, Sillas, Mesas, Archivadores, etc.'");

        Schema::create('bienes_nacionales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_inventario', 50);
            $table->unique('codigo_inventario', 'bienes_codigo_inv_unique');
            $table->index('codigo_inventario', 'bienes_codigo_inv_index');
            $table->foreignId('categoria_bien_id')->constrained('categorias_bienes')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->text('descripcion');
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('serial', 150)->nullable();
            $table->index('serial', 'bienes_serial_index');
            $table->string('color', 50)->nullable();
            $table->string('material', 100)->nullable();
            $table->enum('estatus', ['Disponible', 'Asignado', 'En Mantenimiento', 'Desincorporado'])->default('Disponible');
            $table->index('estatus', 'bienes_estatus_index');
            $table->string('usuario_asignado_nombre', 150)->nullable();
            $table->string('usuario_asignado_cedula', 20)->nullable();
            $table->string('usuario_asignado_cargo', 150)->nullable();
            $table->decimal('valor_adquisicion', 15, 2)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->text('observaciones')->nullable();
            $table->index(['sede_id', 'estatus'], 'bienes_sede_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE bienes_nacionales IS 'Bienes nacionales no tecnológicos de la institución'");

        // ====================================================================
        // BLOQUE 7: CATÁLOGOS DE VEHÍCULOS
        // ====================================================================
        Schema::create('categorias_vehiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->unique('nombre', 'categorias_vehiculos_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE categorias_vehiculos IS 'Catálogo de vehículos: Sedán, Camioneta, Motocicleta, Autobús, etc.'");

        Schema::create('vehiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_inventario', 50);
            $table->unique('codigo_inventario', 'vehiculos_codigo_inv_unique');
            $table->index('codigo_inventario', 'vehiculos_codigo_inv_index');
            $table->foreignId('categoria_vehiculo_id')->constrained('categorias_vehiculos')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->string('placa', 20);
            $table->unique('placa', 'vehiculos_placa_unique');
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->integer('anio')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('serial_motor', 100)->nullable();
            $table->string('serial_chasis', 100)->nullable();
            $table->integer('kilometraje')->default(0);
            $table->enum('estatus', ['Disponible', 'Asignado', 'En Mantenimiento', 'Desincorporado'])->default('Disponible');
            $table->index('estatus', 'vehiculos_estatus_index');
            $table->string('usuario_asignado_nombre', 150)->nullable();
            $table->string('usuario_asignado_cedula', 20)->nullable();
            $table->string('usuario_asignado_cargo', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->index(['sede_id', 'estatus'], 'vehiculos_sede_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE vehiculos IS 'Flota vehicular institucional'");

        // ====================================================================
        // BLOQUE 8: CATÁLOGOS DE EQUIPOS DE SONIDO
        // ====================================================================
        Schema::create('categorias_sonido', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->unique('nombre', 'categorias_sonido_nombre_unique');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE categorias_sonido IS 'Catálogo de equipos de sonido: Parlantes, Micrófonos, Consolas, etc.'");

        Schema::create('equipos_sonido', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_inventario', 50);
            $table->unique('codigo_inventario', 'sonido_codigo_inv_unique');
            $table->index('codigo_inventario', 'sonido_codigo_inv_index');
            $table->foreignId('categoria_sonido_id')->constrained('categorias_sonido')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->string('serial', 150);
            $table->unique('serial', 'sonido_serial_unique');
            $table->string('potencia', 100)->nullable();
            $table->enum('estatus', ['Disponible', 'Asignado', 'En Mantenimiento', 'Desincorporado'])->default('Disponible');
            $table->index('estatus', 'sonido_estatus_index');
            $table->string('usuario_asignado_nombre', 150)->nullable();
            $table->string('usuario_asignado_cedula', 20)->nullable();
            $table->string('usuario_asignado_cargo', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->index(['sede_id', 'estatus'], 'sonido_sede_estatus_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE equipos_sonido IS 'Equipos de audio y sonido institucional'");

        // ====================================================================
        // BLOQUE 9: REGISTROS DE ENTRADA/SALIDA DE BIENES
        // ====================================================================
        Schema::create('registros_entrada_salida', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('tipo', ['Salida', 'Entrada'])->comment('Tipo de movimiento');
            $table->enum('bien_tipo', ['Tecnologia', 'BienNacional', 'Vehiculo', 'Sonido'])->comment('Tipo de bien');
            $table->bigInteger('bien_id')->comment('ID del bien en su tabla respectiva');
            $table->foreignId('sede_id')->nullable()->constrained('sedes')->onDelete('set null')->onUpdate('cascade');
            $table->timestamp('fecha_hora_salida')->nullable();
            $table->timestamp('fecha_hora_entrada')->nullable();
            $table->string('autorizado_por_nombre', 150)->nullable();
            $table->string('autorizado_por_cedula', 20)->nullable();
            $table->string('autorizado_por_cargo', 150)->nullable();
            $table->string('persona_retira_nombre', 150)->nullable();
            $table->string('persona_retira_cedula', 20)->nullable();
            $table->string('persona_retira_cargo', 150)->nullable();
            $table->text('motivo')->nullable();
            $table->text('destino')->nullable();
            $table->string('seguridad_salida_nombre', 150)->nullable();
            $table->string('seguridad_salida_cedula', 20)->nullable();
            $table->string('seguridad_entrada_nombre', 150)->nullable();
            $table->string('seguridad_entrada_cedula', 20)->nullable();
            $table->enum('estado_salida', ['Operativo', 'Con Daños', 'Incompleto'])->nullable();
            $table->enum('estado_entrada', ['Operativo', 'Con Daños', 'Incompleto', 'No Retornó'])->nullable();
            $table->text('observaciones_salida')->nullable();
            $table->text('observaciones_entrada')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->index('fecha_hora_salida', 'registros_es_fecha_salida_index');
            $table->index(['bien_tipo', 'bien_id'], 'registros_es_bien_index');
            $table->index(['sede_id', 'tipo'], 'registros_es_sede_tipo_index');
            $table->timestamps();
        });
        DB::statement("COMMENT ON TABLE registros_entrada_salida IS 'Registro de movimientos de bienes con autorización y seguridad'");

        // ====================================================================
        // BLOQUE 10: BITÁCORAS
        // ====================================================================
        Schema::create('bitacora_equipos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->string('accion', 50);
            $table->text('descripcion_detallada');
            $table->jsonb('datos_anteriores')->nullable();
            $table->jsonb('datos_nuevos')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->index('fecha_registro', 'bitacora_fecha_index');
            $table->index(['equipo_id', 'fecha_registro'], 'bitacora_equipo_fecha_index');
            $table->index(['usuario_id', 'fecha_registro'], 'bitacora_usuario_fecha_index');
            $table->index('accion', 'bitacora_accion_index');
        });
        DB::statement("COMMENT ON TABLE bitacora_equipos IS 'Bitácora de cambios en equipos tecnológicos'");

        Schema::create('bitacora_componentes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('componente_id')->constrained('componentes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->string('accion', 50);
            $table->text('descripcion_detallada');
            $table->jsonb('datos_anteriores')->nullable();
            $table->jsonb('datos_nuevos')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->index('fecha_registro', 'bitacora_comp_fecha_index');
            $table->index(['componente_id', 'fecha_registro'], 'bitacora_comp_componente_fecha_index');
            $table->index('accion', 'bitacora_comp_accion_index');
        });
        DB::statement("COMMENT ON TABLE bitacora_componentes IS 'Bitácora de cambios en componentes'");

        // ====================================================================
        // BLOQUE 11: RBAC (Roles y Permisos - Spatie)
        // ====================================================================
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('guard_name', 100);
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('guard_name', 100);
            $table->string('modulo', 50)->nullable()->comment('Módulo al que pertenece: tecnologia, bienes, vehiculos, sonido, entrada-salida, usuarios, roles');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permisos')->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('model_id');
            $table->string('model_type', 255)->default('App\\Models\\User');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('user_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('permisos')->onDelete('cascade');
            $table->foreignId('model_id');
            $table->string('model_type', 255)->default('App\\Models\\User');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        // ====================================================================
        // BLOQUE 12: SESIONES Y CACHÉ
        // ====================================================================
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('user_has_permissions');
        Schema::dropIfExists('user_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('bitacora_componentes');
        Schema::dropIfExists('bitacora_equipos');
        Schema::dropIfExists('registros_entrada_salida');
        Schema::dropIfExists('equipos_sonido');
        Schema::dropIfExists('categorias_sonido');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('categorias_vehiculos');
        Schema::dropIfExists('bienes_nacionales');
        Schema::dropIfExists('categorias_bienes');
        Schema::dropIfExists('bitacora_acceso_contrasenas');
        Schema::dropIfExists('equipo_sistemas_operativos');
        Schema::dropIfExists('ordenes_servicio');
        Schema::dropIfExists('equipo_componente');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('componentes');
        Schema::dropIfExists('categorias_componentes');
        Schema::dropIfExists('tipos_equipos');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('departamentos');
        Schema::dropIfExists('sedes');
        Schema::dropIfExists('estados');
    }
};