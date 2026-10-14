/**
 * @vitest-environment node
 */
import { describe, expect, it } from 'vitest';
import {
    resolveCreatureContextFromPageProps,
    resolveRuntimeModelForKey,
} from '@/Composables/characteristic/resolveRuntimeModelForKey.js';

describe('resolveRuntimeModelForKey', () => {
    it('fusionne computed runtime et base', () => {
        const runtime = {
            computed: {
                initiative_creature: {
                    total: 12,
                    base: 8,
                    object: 3,
                    context: 1,
                    ephemeral: 0,
                    substituted: '8+3+1',
                    placeholders: [{ key: 'x', label: 'Base', value: 8 }],
                },
            },
        };

        const vm = resolveRuntimeModelForKey(runtime, 'initiative_creature', {
            key: 'initiative_creature',
            name: 'Initiative',
        });

        expect(vm.total).toBe(12);
        expect(vm.base).toBe(8);
        expect(vm.object).toBe(3);
        expect(vm.context).toBe(1);
        expect(vm.substituted).toBe('8+3+1');
        expect(vm.name).toBe('Initiative');
    });

    it('renvoie null sans entrée runtime', () => {
        expect(resolveRuntimeModelForKey({ computed: {} }, 'missing')).toBeNull();
    });
});

describe('resolveCreatureContextFromPageProps', () => {
    it('détecte monstre + runtime Inertia', () => {
        const ctx = resolveCreatureContextFromPageProps({
            monster: { creature_id: 42 },
            characteristicRuntime: { computed: { a: { total: 1 } } },
        });
        expect(ctx.creatureId).toBe(42);
        expect(ctx.entityQuery).toBe('monster');
        expect(ctx.runtime.computed.a.total).toBe(1);
    });

    it('détecte PNJ en édition sans runtime préchargé', () => {
        const ctx = resolveCreatureContextFromPageProps({
            npc: { creature: { id: 7 } },
        });
        expect(ctx.creatureId).toBe(7);
        expect(ctx.entityQuery).toBe('npc');
        expect(ctx.runtime).toBeNull();
    });

    it('renvoie null hors contexte créature', () => {
        expect(resolveCreatureContextFromPageProps({ spell: { id: 1 } })).toBeNull();
    });
});
