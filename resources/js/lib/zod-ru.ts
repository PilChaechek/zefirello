import { z } from 'zod';

export const setZodRuLocale = () => {
    const customErrorMap: z.ZodErrorMap = (issue, ctx) => {
        if (issue.code === z.ZodIssueCode.invalid_type) {
            if (issue.received === "undefined" || issue.received === "null") {
                return { message: "Обязательное поле" };
            }
        }
        if (issue.code === z.ZodIssueCode.too_small) {
            if (issue.type === 'string') {
                return { message: `Минимум ${issue.minimum} символов` };
            }
        }
        // Тут можно добавить другие переводы
        return { message: ctx.defaultError };
    };

    z.setErrorMap(customErrorMap);
};
