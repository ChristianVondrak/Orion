// Paleta de colores principal
export const mainColors = {
    primary: '#2563EB',    // Azul principal
    secondary: '#4F46E5',  // Índigo
    success: '#059669',    // Verde
    warning: '#D97706',    // Ámbar
    danger: '#DC2626',     // Rojo
    info: '#0891B2',       // Cyan
    purple: '#7C3AED',     // Violeta
    gray: '#4B5563'        // Gris neutro
};

// Paleta de colores para gráficos
export const chartColors = {
    // Colores para gráficos de barras/líneas
    sequential: [
        '#2563EB', // primary
        '#4F46E5', // secondary
        '#7C3AED', // purple
        '#0891B2', // info
        '#059669', // success
    ],
    
    // Colores para gráficos de estado
    status: {
        optimal: '#059669',    // Verde para estados óptimos
        moderate: '#D97706',   // Ámbar para estados moderados
        low: '#DC2626',        // Rojo para estados bajos
        over: '#7C3AED'        // Violeta para estados excedidos
    },
    
    // Colores para gráficos de género
    gender: {
        male: '#2563EB',      // Azul para masculino
        female: '#EC4899'     // Rosa para femenino
    },
    
    // Colores para gráficos de compensación
    compensation: {
        fixed: '#4B5563',     // Gris para pago fijo
        hourly: '#2563EB'     // Azul para pago por hora
    },
    
    // Colores de fondo y bordes
    background: {
        light: '#F3F4F6',     // Gris claro para fondos
        white: '#FFFFFF'      // Blanco
    },
    text: {
        primary: '#1F2937',   // Gris oscuro para texto principal
        secondary: '#6B7280', // Gris medio para texto secundario
        light: '#9CA3AF'      // Gris claro para texto terciario
    }
};

// Variables para diseño consistente
export const designTokens = {
    borderRadius: '0.5rem',
    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
    transition: 'all 0.3s ease'
}; 