import { createAPI } from './apiFactory.js';
export const studentsSubjectsAPI = createAPI('studentsSubjects');

/**
 * Ejemplo de extensión de la API:
*/
// import { createAPI } from './apiFactory.js';
// const baseAPI = createAPI('studentsSubjects');

// export const studentsSubjectsAPI = 
// {
//     ...baseAPI, // hereda fetchAll, create, update, remove

//     // método adicional personalizado
//     async fetchByStudentId(id) 
//     {
//         const res = await fetch(`../../backend/server.php?module=studentsSubjects&studentId=${id}`);
//         if (!res.ok) throw new Error("No se pudieron obtener asignaciones del estudiante");
//         return await res.json();
//     }
// };

/**
 * También permite url personalizadas ahora:
*/
// const customAPI = createAPI('custom', 
// {
//     urlOverride: '../../backend/misRutas/personalizadas.php'
// });
