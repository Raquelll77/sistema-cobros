<div class="contenedor-95">
    <div class="ui grid stackable">
        <!-- Columna izquierda: TABLA -->
        <div class="twelve wide column">
            <table class="ui celled striped compact selectable table" id="tbl-usuarios-simple">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th class="one wide">Estado</th>
                        <th class="two wide">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr data-id="<?= htmlspecialchars($usuario->id) ?>">
                            <td><?= htmlspecialchars($usuario->nombre) ?></td>
                            <td><?= htmlspecialchars($usuario->usuario) ?></td>
                            <td>
                                <div class="ui label">
                                    <i class="id badge icon"></i>
                                    <?= htmlspecialchars($usuario->rol) ?>
                                </div>
                            </td>
                            <td>
                                <?php if ((int) $usuario->estado === 1): ?>
                                    <div class="ui green tiny label">Activo</div>
                                <?php else: ?>
                                    <div class="ui red tiny label">Inactivo</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="ui tiny buttons">
                                    <?php if ((int) $usuario->estado === 1): ?>
                                        <a class="ui orange button"
                                            href="/configuracion/usuarios-inhabilitar?id=<?= urlencode($usuario->id) ?>">
                                            <i class="ban icon"></i>
                                        </a>
                                    <?php else: ?>
                                        <a class="ui green button"
                                            href="/configuracion/usuarios-habilitar?id=<?= urlencode($usuario->id) ?>">
                                            <i class="check icon"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="#" class="ui blue button btn-editar-usuario"
                                        data-id="<?= htmlspecialchars($usuario->id) ?>"
                                        data-usuario="<?= htmlspecialchars($usuario->usuario) ?>"
                                        data-nombre="<?= htmlspecialchars($usuario->nombre) ?>"
                                        data-rol="<?= htmlspecialchars($usuario->rol) ?>"
                                        data-estado="<?= (int) $usuario->estado ?>">
                                        <i class="edit icon"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Columna derecha: FORMULARIO CREAR -->
        <div class="four wide column">
            <div class="ui top attached header">
                <i class="plus icon"></i>
                <div class="content">Nuevo usuario</div>
            </div>
            <div class="ui attached segment">
                <form class="ui form" id="frm-nuevo-usuario">
                    <input type="hidden" name="id" id="usuario_id">

                    <div class="field required">
                        <label>Usuario</label>
                        <input type="text" name="usuario" id="f_usuario" placeholder="usuario.ejemplo" required>
                    </div>

                    <div class="field required">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="f_nombre" placeholder="Nombre completo" required>
                    </div>
                    <div class="field required create-only" id="wrap_password">
                        <label>Contraseña</label>
                        <input type="password" name="password" id="f_password" placeholder="••••••••" required>
                    </div>


                    <!-- SOLO los 2 selects dentro de two fields -->
                    <div class="two fields">
                        <div class="field required">
                            <label>Rol</label>
                            <select name="rol" id="f_rol" class="ui dropdown" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="SUPERVISOR">SUPERVISOR</option>
                                <option value="TELECOBRO">TELECOBRO</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fuera de two fields -->
                    <div class="field">
                        <div class="ui checkbox">
                            <input type="checkbox" name="estado" id="f_estado" value="1" checked>
                            <label>Usuario activo</label>
                        </div>
                    </div>

                    <div class="ui buttons">
                        <button type="submit" class="ui green button" id="btn-guardar">
                            <i class="save icon"></i> Guardar
                        </button>
                        <div class="or"></div>
                        <button type="button" class="ui button" id="btn-cancelar">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hasJQ = typeof window.jQuery !== 'undefined';
        if (hasJQ) {
            try {
                $('.ui.dropdown').dropdown();
                $('.ui.checkbox').checkbox();
            } catch (e) { console.warn('Init Fomantic falló:', e); }
        }

        const tbody = document.querySelector('#tbl-usuarios-simple tbody');
        const form = document.getElementById('frm-nuevo-usuario');

        const hId = document.getElementById('usuario_id');
        const fUsuario = document.getElementById('f_usuario');
        const fNombre = document.getElementById('f_nombre');
        const fRol = document.getElementById('f_rol');
        const fEstado = document.getElementById('f_estado');
        const fPassword = document.getElementById('f_password');     // <- nuevo
        const wrapPassword = document.getElementById('wrap_password');  // <- nuevo
        const btnGuardar = document.getElementById('btn-guardar');
        const btnCancelar = document.getElementById('btn-cancelar');

        let editMode = false;

        // Helpers
        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function goEditMode() {
            editMode = true;
            // ocultar contraseña en edición
            if (wrapPassword) wrapPassword.style.display = 'none';
            if (fPassword) { fPassword.required = false; fPassword.value = ''; }

            btnGuardar.innerHTML = '<i class="save icon"></i> Actualizar';
            btnGuardar.classList.remove('green');
            btnGuardar.classList.add('blue');
        }

        function goCreateMode() {
            editMode = false;
            // mostrar contraseña en creación
            if (wrapPassword) wrapPassword.style.display = '';
            if (fPassword) { fPassword.required = true; fPassword.value = ''; }

            btnGuardar.innerHTML = '<i class="save icon"></i> Guardar';
            btnGuardar.classList.remove('blue');
            btnGuardar.classList.add('green');
        }

        function resetForm() {
            form.reset();
            hId.value = '';
            if (hasJQ) {
                $('#f_rol').dropdown('clear');
                $('.ui.checkbox').checkbox('check');
            } else {
                fRol.value = '';
                fEstado.checked = true;
            }
            document.querySelectorAll('#tbl-usuarios-simple tbody tr').forEach(tr => tr.classList.remove('active'));
            goCreateMode();
        }

        function updateEditBtnDataset(btn, u) {
            btn.setAttribute('data-id', String(u.id));
            btn.setAttribute('data-usuario', u.usuario ?? '');
            btn.setAttribute('data-nombre', u.nombre ?? '');
            btn.setAttribute('data-rol', u.rol ?? '');
            btn.setAttribute('data-estado', String(Number(u.estado) === 1 ? 1 : 0));
        }

        // CLICK en Editar -> cargar form desde data-*
        tbody.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-editar-usuario');
            if (!btn) return;
            e.preventDefault();

            const { id, usuario, nombre, rol, estado } = btn.dataset;

            hId.value = id || '';
            fUsuario.value = usuario || '';
            fNombre.value = nombre || '';
            if (hasJQ) {
                $('#f_rol').dropdown('set selected', rol || '');
                $('#f_estado').prop('checked', Number(estado) === 1);
                if (Number(estado) === 1) $('.ui.checkbox').checkbox('check');
                else $('.ui.checkbox').checkbox('uncheck');
            } else {
                fRol.value = rol || '';
                fEstado.checked = Number(estado) === 1;
            }

            goEditMode();

            document.querySelectorAll('#tbl-usuarios-simple tbody tr').forEach(tr => tr.classList.remove('active'));
            btn.closest('tr').classList.add('active');
        });

        // SUBMIT crear/actualizar
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const fd = new FormData(form);
            fd.set('estado', fEstado.checked ? '1' : '0');

            if (editMode) {
                // en edición NO enviar password
                fd.delete('password');
            } else {
                // creación: exigir password
                const pass = (fd.get('password') || '').toString().trim();
                if (!pass) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo requerido',
                        text: 'La contraseña es requerida para crear el usuario.',
                        confirmButtonColor: '#f1c40f'
                    });
                    fPassword?.focus();
                    return;
                }
            }

            try {
                const resp = await fetch('/configuracion/usuarios-guardar', { method: 'POST', body: fd });
                const ctype = resp.headers.get('content-type') || '';

                if (!resp.ok) {
                    const txt = ctype.includes('application/json') ? JSON.stringify(await resp.json()) : await resp.text();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error HTTP',
                        html: `<p>Código: ${resp.status}</p><pre>${txt}</pre>`,
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                const json = ctype.includes('application/json') ? await resp.json() : { ok: false, message: 'Respuesta no JSON' };

                if (!json.ok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: json.message || 'No se pudo guardar.',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                const u = json.data; // {id, usuario, nombre, rol, estado}

                if (editMode) {
                    // actualizar fila existente
                    const row = document.querySelector(`#tbl-usuarios-simple tbody tr[data-id="${CSS.escape(String(u.id))}"]`);
                    if (row) {
                        row.querySelector('td:nth-child(1)').textContent = u.nombre;
                        row.querySelector('td:nth-child(2)').textContent = u.usuario;
                        row.querySelector('td:nth-child(3) .ui.label').lastChild.nodeValue = ' ' + u.rol;
                        row.querySelector('td:nth-child(4)').innerHTML =
                            Number(u.estado) === 1 ? '<div class="ui green tiny label">Activo</div>'
                                : '<div class="ui red tiny label">Inactivo</div>';

                        const editBtn = row.querySelector('.btn-editar-usuario');
                        if (editBtn) updateEditBtnDataset(editBtn, u);
                    }
                } else {
                    // insertar nueva fila arriba
                    const tr = document.createElement('tr');
                    tr.setAttribute('data-id', String(u.id));
                    tr.innerHTML = `
                <td>${escapeHtml(u.nombre)}</td>
                <td>${escapeHtml(u.usuario)}</td>
                <td><div class="ui label"><i class="id badge icon"></i> ${escapeHtml(u.rol)}</div></td>
                <td>${Number(u.estado) === 1 ? '<div class="ui green tiny label">Activo</div>' : '<div class="ui red tiny label">Inactivo</div>'}</td>
                <td>
                    <div class="ui tiny buttons">
                        ${Number(u.estado) === 1
                            ? `<a class="ui orange button" href="/configuracion/inhabilitar_usuario?id=${encodeURIComponent(u.id)}"><i class="ban icon"></i></a>`
                            : `<a class="ui green button" href="/configuracion/habilitar_usuario?id=${encodeURIComponent(u.id)}"><i class="check icon"></i></a>`}
                        <a href="#" class="ui blue button btn-editar-usuario"
                           data-id="${escapeHtml(u.id)}"
                           data-usuario="${escapeHtml(u.usuario)}"
                           data-nombre="${escapeHtml(u.nombre)}"
                           data-rol="${escapeHtml(u.rol)}"
                           data-estado="${Number(u.estado)}">
                            <i class="edit icon"></i>
                        </a>
                    </div>
                </td>`;
                    tbody.prepend(tr);
                }

                resetForm();

                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: json.message || (editMode ? 'Usuario actualizado correctamente' : 'Usuario creado correctamente'),
                    confirmButtonColor: '#3085d6'
                });

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'Ocurrió un problema en la comunicación con el servidor.',
                    confirmButtonColor: '#d33'
                });
            }
        });

        // Cancelar → volver a “Nuevo”
        btnCancelar?.addEventListener('click', resetForm);

        // Click fuera del formulario → volver a “Nuevo”
        document.addEventListener('click', function (e) {
            const clickedInsideForm = e.target.closest('#frm-nuevo-usuario');
            const clickedEditBtn = e.target.closest('.btn-editar-usuario');
            if (clickedInsideForm || clickedEditBtn) return;
            resetForm();
        });

        // Inicia en modo crear
        goCreateMode();
    });



    // Delegación: click en habilitar/inhabilitar
    tbody.addEventListener('click', async function (e) {
        const a = e.target.closest('a');
        if (!a) return;

        const href = a.getAttribute('href') || '';
        const isInhabilitar = href.includes('/configuracion/usuarios-inhabilitar');
        const isHabilitar = href.includes('/configuracion/usuarios-habilitar');
        if (!isInhabilitar && !isHabilitar) return;

        e.preventDefault();

        const row = a.closest('tr');
        const userId = row?.getAttribute('data-id');
        if (!userId) return;

        // Confirm
        const { isConfirmed } = await Swal.fire({
            icon: 'question',
            title: isInhabilitar ? 'Inhabilitar usuario' : 'Habilitar usuario',
            text: isInhabilitar
                ? 'El usuario no podrá acceder al sistema. ¿Continuar?'
                : 'El usuario podrá acceder al sistema. ¿Continuar?',
            showCancelButton: true,
            confirmButtonText: isInhabilitar ? 'Sí, inhabilitar' : 'Sí, habilitar',
            cancelButtonText: 'Cancelar'
        });
        if (!isConfirmed) return;

        // Llamada AJAX
        try {
            const url = isInhabilitar
                ? `/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(userId)}`
                : `/configuracion/usuarios-habilitar?id=${encodeURIComponent(userId)}`;

            const resp = await fetch(url, {
                method: 'POST', // usa POST para cambiar estado
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            // Soporte si tu controlador redirige cuando no es AJAX
            const ctype = resp.headers.get('content-type') || '';
            if (resp.status === 401) { window.location = '/login'; return; }
            if (resp.status === 403) {
                Swal.fire({ icon: 'error', title: 'Sin permisos', text: 'No tienes acceso a esta acción.' });
                return;
            }
            if (!resp.ok) {
                const txt = ctype.includes('application/json') ? JSON.stringify(await resp.json()) : await resp.text();
                Swal.fire({ icon: 'error', title: 'Error', html: `<pre>${txt}</pre>` });
                return;
            }

            // Si devuelve JSON
            let json = { ok: true, estado: isHabilitar ? 1 : 0, message: '' };
            if (ctype.includes('application/json')) json = await resp.json();
            if (!json.ok) {
                Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: json.message || '' });
                return;
            }

            // Actualizar UI de la fila
            const nuevoEstado = Number(json.estado ?? (isHabilitar ? 1 : 0));
            // Columna estado (4ta)
            row.querySelector('td:nth-child(4)').innerHTML = nuevoEstado === 1
                ? '<div class="ui green tiny label">Activo</div>'
                : '<div class="ui red tiny label">Inactivo</div>';

            // Botones (5ta) — re-render
            const accionesTd = row.querySelector('td:nth-child(5) .ui.tiny.buttons');
            accionesTd.innerHTML = `
      ${nuevoEstado === 1
                    ? `<a class="ui orange button" href="/configuracion/usuarios-inhabilitar?id=${encodeURIComponent(userId)}"><i class="ban icon"></i></a>`
                    : `<a class="ui green button" href="/configuracion/usuarios-habilitar?id=${encodeURIComponent(userId)}"><i class="check icon"></i></a>`}
      <a href="#" class="ui blue button btn-editar-usuario"
         data-id="${escapeHtml(userId)}"
         data-usuario="${escapeHtml(row.querySelector('td:nth-child(2)').textContent.trim())}"
         data-nombre="${escapeHtml(row.querySelector('td:nth-child(1)').textContent.trim())}"
         data-rol="${escapeHtml(row.querySelector('td:nth-child(3) .ui.label').innerText.trim())}"
         data-estado="${nuevoEstado}">
        <i class="edit icon"></i>
      </a>
    `;

            Swal.fire({
                icon: 'success',
                title: 'Hecho',
                text: json.message || (nuevoEstado === 1 ? 'Usuario habilitado' : 'Usuario inhabilitado')
            });
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Error de red', text: 'No se pudo completar la acción.' });
        }
    });

</script>