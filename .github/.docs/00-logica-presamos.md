Basado en la estructura de tu base de datos (específicamente las tablas `clients` y `loans`), te explico la lógica necesaria para determinar si un cliente puede recibir un préstamo. Esta lógica se basa en criterios de riesgo crediticio comunes en sistemas de préstamos, priorizando la protección financiera tanto del cliente como de la institución. La evaluación debe ser automática (a través de código en un servicio o modelo) y manual (revisión por un analista si es necesario).

### **Criterios Principales para Aprobar un Préstamo**

La decisión se toma evaluando varios factores del cliente. Asumiendo que el monto solicitado es `requested_amount`, la frecuencia de pago es `payment_frequency` (ej. mensual), y el número de cuotas es `installments`, aquí va la lógica paso a paso:

1. **Verificación del Estado del Cliente**:

    - Campo: `clients.status`
    - Lógica: El cliente debe estar en estado `active` (usando el enum `ClientStatus`). Si está `inactive`, `suspended` o `blacklisted`, rechazar automáticamente.
    - Razón: Evita otorgar préstamos a clientes con problemas previos.

2. **Verificación de Identidad y Edad**:

    - Campos: `clients.document_number`, `clients.birth_date`
    - Lógica:
        - El `document_number` no debe estar vacío y debe ser único (ya está indexado como único).
        - Calcular la edad: `edad = hoy - birth_date`. Debe ser >= 18 años (o el límite legal en tu país).
    - Razón: Cumplimiento legal y verificación básica de identidad.

3. **Evaluación del Límite de Crédito Disponible**:

    - Campos: `clients.available_credit_limit`, `clients.max_credit_limit`, `clients.used_credit_limit`
    - Lógica:
        - `available_credit_limit` debe ser > 0 y >= `requested_amount`.
        - Si no, rechazar. Este campo se calcula automáticamente como `max_credit_limit - used_credit_limit`.
    - Razón: Evita sobreendeudamiento. El `used_credit_limit` se actualiza con la suma de saldos pendientes de préstamos activos.

4. **Revisión de Historial Crediticio (Préstamos Activos y en Mora)**:

    - Campos en `loans`: `status`, `arrears_balance`, `total_balance`, `client_id`
    - Lógica:
        - Consultar préstamos del cliente donde `status` sea `active` o `overdue`.
        - Si algún préstamo tiene `arrears_balance > 0`, rechazar (indica mora).
        - Calcular el total de `total_balance` de préstamos activos y sumarlo al `requested_amount` para verificar contra `available_credit_limit`.
    - Razón: Clientes con mora representan mayor riesgo. (Nota: Si tienes una tabla `installments` o `payments`, podrías verificar pagos atrasados específicos, pero basándonos en lo disponible, esto es suficiente).

5. **Evaluación de Capacidad de Pago**:

    - Campos: `clients.monthly_income`, y cálculos basados en el préstamo solicitado.
    - Lógica:
        - Estimar la cuota mensual: Usar fórmula de amortización (capital + intereses) / cuotas, ajustada por `payment_frequency`.
            - Ejemplo simple: `cuota_mensual = (requested_amount * (1 + interest_rate_percentage/100)) / installments`.
        - Verificar: `monthly_income * 0.3` (30% del ingreso) >= `cuota_mensual`. (El porcentaje es configurable, ej. 20-40% según políticas).
        - Si no, rechazar o reducir el monto/cuotas.
    - Razón: Asegura que el cliente pueda pagar sin dificultades. Incluye ingresos de `occupation` como factor cualitativo.

6. **Factores Adicionales (Opcionales pero Recomendados)**:
    - **Referencias y Notas**: Revisar `personal_references` y `notes` manualmente para riesgos subjetivos (ej. historial laboral inestable).
    - **Ocupación e Ingreso**: Si `monthly_income` es bajo o `occupation` indica inestabilidad, aplicar mayor escrutinio.
    - **Historial de Pagos Global**: Si el cliente ha tenido préstamos pagados (`status = paid`), dar puntos positivos; si muchos `overdue`, penalizar.
    - **Límite de Préstamos Simultáneos**: Máximo 1-2 préstamos activos por cliente (configurable).

### **Flujo de Decisión General**

-   **Aprobación Automática**: Si cumple todos los criterios (1-5), aprobar.
-   **Rechazo Automático**: Si falla en 1, 2, 3 o 4.
-   **Revisión Manual**: Si falla en capacidad de pago (5) o hay factores adicionales, enviar a analista.
-   **Cálculos Adicionales**:
    -   Actualizar `used_credit_limit` al aprobar: `used_credit_limit += requested_amount`.
    -   Generar código único para el préstamo (`loans.code`).
    -   Calcular fechas de vencimiento basadas en `first_due_date` y `payment_frequency`.

### **Implementación en Código (Ejemplo en Laravel)**

Podrías crear un método en el modelo `Client` o un servicio:

```php
public function canReceiveLoan(float $requestedAmount, int $installments, float $interestRate): bool
{
    // 1. Estado activo
    if ($this->status !== ClientStatus::active) return false;

    // 2. Edad >= 18
    if ($this->birth_date && now()->diffInYears($this->birth_date) < 18) return false;

    // 3. Cupo disponible
    if ($this->available_credit_limit < $requestedAmount) return false;

    // 4. Sin mora
    $hasArrears = $this->loans()->where('arrears_balance', '>', 0)->exists();
    if ($hasArrears) return false;

    // 5. Capacidad de pago (ejemplo simple)
    $monthlyPayment = ($requestedAmount * (1 + $interestRate / 100)) / $installments;
    if ($this->monthly_income * 0.3 < $monthlyPayment) return false;

    return true;
}
```

### **Recomendaciones**

-   **Scoring Crediticio**: Implementa un sistema de puntuación (ej. 0-100) basado en estos factores para decisiones más nuancadas.
-   **Auditoría**: Registra decisiones en logs o tablas de auditoría usando `created_by`/`updated_by`.
-   **Pruebas**: Crea tests para estos escenarios (ej. cliente con mora no aprueba).
-   **Regulaciones**: Asegúrate de cumplir con leyes locales sobre préstamos (ej. tasas máximas, disclosures).

## TODO Más Importante

Usar funciones o métodos en el modelo para hacer que los cálculos sean indiferentes al frontend y se hagan desde el backend en el propio modelo.
