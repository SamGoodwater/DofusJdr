// @vitest-environment node
import { describe, expect, it } from 'vitest';
import { buildSelectOptionBadgeProps } from '../../../resources/js/Utils/Entity/selectOptionBadge.js';
import { getSpellCategoryOptions } from '../../../resources/js/Utils/Entity/SharedConstants.js';

describe('buildSelectOptionBadgeProps — catégories de sort', () => {
    const cfg = { enabled: true, variant: '' };

    it('utilise la couleur DaisyUI de l’option (pas de pastels auto)', () => {
        for (const opt of getSpellCategoryOptions()) {
            const badge = buildSelectOptionBadgeProps(opt, cfg);
            expect(badge.color).toBe(opt.color);
            expect(badge.autoScheme).toBeUndefined();
            expect(badge.strong).toBe(true);
            expect(badge.variant).toBe('');
        }
    });

    it('ignore labelHash si l’option porte déjà une couleur', () => {
        const badge = buildSelectOptionBadgeProps(
            { value: 1, label: 'Sort de créature', color: 'warning' },
            {
                enabled: true,
                color: 'auto',
                autoScheme: 'labelHash',
                autoTone: 'light',
                variant: 'soft',
            },
        );
        expect(badge.color).toBe('warning');
        expect(badge.autoScheme).toBeUndefined();
    });
});
