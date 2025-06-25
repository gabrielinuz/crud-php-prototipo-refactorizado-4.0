import { subjectsAPI } from '../api/subjectsAPI.js';

document.addEventListener('DOMContentLoaded', () => 
{
    loadSubjects();
    setupSubjectFormHandler();
});

function setupSubjectFormHandler() 
{
    const form = document.getElementById('subjectForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();

        const formData = new FormData();
        formData.append('id', document.getElementById('subjectId').value.trim());
        formData.append('name', document.getElementById('name').value.trim());

        const fileInput = document.getElementById('syllabus');
        if (fileInput.files.length > 0) 
        {
            formData.append('syllabus', fileInput.files[0]);
        }

        try
        {
            const id = formData.get('id');
            if (id) 
            {
                await subjectsAPI.update(formData);
            }
            else
            {
                await subjectsAPI.create(formData);
            }

            form.reset();
            document.getElementById('subjectId').value = '';
            loadSubjects();
        }
        catch (err)
        {
            console.error(err.message);
        }
    });
}

async function loadSubjects()
{
    try
    {
        const subjects = await subjectsAPI.fetchAll();
        renderSubjectTable(subjects);
    }
    catch (err)
    {
        console.error('Error cargando materias:', err.message);
    }
}

function renderSubjectTable(subjects)
{
    const tbody = document.getElementById('subjectTableBody');
    tbody.replaceChildren();

    subjects.forEach(subject =>
    {
        const row = renderSubjectRow(subject);
        tbody.appendChild(row);
    });
}

function renderSubjectRow(subject)
{
    const tr = document.createElement('tr');

    // Nombre
    tr.appendChild(createCell(subject.name));

    // Enlace al PDF
    const pdfCell = document.createElement('td');
    if (subject.syllabus_path)
    {
        const link = document.createElement('a');
        link.href = `../../backend/${subject.syllabus_path}`;
        link.target = '_blank';
        link.textContent = 'Ver PDF';
        pdfCell.appendChild(link);
    }
    else
    {
        pdfCell.textContent = '—';
    }
    tr.appendChild(pdfCell);

    // Acciones
    tr.appendChild(createSubjectActionsCell(subject));

    return tr;
}

function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createSubjectActionsCell(subject)
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => {
        document.getElementById('subjectId').value = subject.id;
        document.getElementById('name').value = subject.name;
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDeleteSubject(subject.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

async function confirmDeleteSubject(id)
{
    if (!confirm('¿Seguro que deseas borrar esta materia?')) return;

    try
    {
        await subjectsAPI.remove(id);
        loadSubjects();
    }
    catch (err)
    {
        console.error('Error al borrar materia:', err.message);
    }
}
