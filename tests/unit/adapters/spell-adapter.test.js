/**
 * Tests unitaires pour spell-adapter (version simplifiée)
 *
 * @description
 * Vérifie que :
 * - adaptSpellEntitiesTableResponse transforme correctement les données
 * - Les entités brutes sont converties en instances de Spell
 * - Les cellules ne sont plus pré-générées (elles sont vides)
 * - L'instance Spell est passée dans rowParams.entity pour génération à la volée
 */

import { describe, it, expect } from 'vitest';
import { adaptSpellEntitiesTableResponse } from '@/Entities/spell/spell-adapter';
import { Spell } from '@/Models/Entity/Spell';

describe('spell-adapter (version simplifiée)', () => {
    describe('adaptSpellEntitiesTableResponse', () => {
        it('transforme entities en TableResponse avec instances Spell', () => {
            const response = {
                meta: {
                    entityType: 'spells',
                    query: { search: '', sort: 'id', order: 'desc', limit: 10 },
                    capabilities: { viewAny: true, updateAny: true },
                },
                entities: [
                    { id: 1, name: 'Spell 1', level: '10', pa: '3', po: '2' },
                    { id: 2, name: 'Spell 2', level: '20', pa: '4', po: '3' },
                ],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('spells');
            expect(result.rows).toHaveLength(2);
            expect(result.rows[0].id).toBe(1);
            
            // Les cellules ne sont plus pré-générées
            expect(result.rows[0].cells).toEqual({});
            
            // L'instance Spell est passée dans rowParams.entity
            expect(result.rows[0].rowParams.entity).toBeInstanceOf(Spell);
            expect(result.rows[0].rowParams.entity.id).toBe(1);
            expect(result.rows[0].rowParams.entity.name).toBe('Spell 1');
            expect(result.rows[0].rowParams.entity.level).toBe('10');
            expect(result.rows[0].rowParams.entity.pa).toBe('3');
            expect(result.rows[0].rowParams.entity.po).toBe(2);
        });

        it('gère un tableau vide', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.meta.entityType).toBe('spells');
            expect(result.rows).toHaveLength(0);
        });

        it('gère entities null ou undefined', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: null,
            };

            const result = adaptSpellEntitiesTableResponse(response);

            expect(result.rows).toHaveLength(0);
        });

        it('préserve toutes les propriétés de l\'entité dans l\'instance Spell', () => {
            const entity = { id: 1, name: 'Test', level: '15', pa: '4', po: '3', customField: 'custom' };
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [entity],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            const spell = result.rows[0].rowParams.entity;
            expect(spell).toBeInstanceOf(Spell);
            expect(spell.id).toBe(1);
            expect(spell.name).toBe('Test');
            expect(spell.level).toBe('15');
            expect(spell.pa).toBe('4');
            expect(spell.po).toBe(3);
            // Les champs personnalisés sont préservés dans _data
            expect(spell._data?.customField).toBe('custom');
        });

        it('gère les valeurs nulles correctement', () => {
            const response = {
                meta: { entityType: 'spells', query: {}, capabilities: {} },
                entities: [
                    { id: 1, name: 'Test', level: null, pa: null, po: null },
                ],
            };

            const result = adaptSpellEntitiesTableResponse(response);

            const spell = result.rows[0].rowParams.entity;
            expect(spell).toBeInstanceOf(Spell);
            expect(spell.id).toBe(1);
            expect(spell.name).toBe('Test');
            expect(spell.level).toBeNull();
            expect(spell.pa).toBeNull();
            expect(spell.po).toBeNull();
        });
    });
});

