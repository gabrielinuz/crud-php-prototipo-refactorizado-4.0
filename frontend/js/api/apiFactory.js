export function createAPI(moduleName, config = {}) 
{
    const API_URL = config.urlOverride ?? `../../backend/server.php?module=${moduleName}`;

    async function send(method, data)
    {
        const isFormData = data instanceof FormData;

        const res = await fetch(API_URL,
        {
            method,
            headers: isFormData ? {} : { 'Content-Type': 'application/json' },
            body: isFormData ? data : JSON.stringify(data)
        });

        if (!res.ok) throw new Error(`Error en ${method}`);
        return await res.json();
    }

    return {
        async fetchAll()
        {
            const res = await fetch(API_URL);
            if (!res.ok) throw new Error("No se pudieron obtener los datos");
            return await res.json();
        },
        async create(data)  { return await send('POST', data); },
        async update(data)  { return await send('PUT',  data); },
        async remove(id)    { return await send('DELETE', { id }); }
    };
}
