/**
 * Dispatcher central des actions d'entités.
 *
 * @description
 * Mutualise les effets standards : navigation, modal, copie de lien, refresh et suppression.
 * La suppression expose un état de ConfirmModal (récap impact) plutôt qu’un `window.confirm`.
 *
 * @example
 * const { dispatchEntityAction, deleteConfirm, confirmPendingDelete, cancelPendingDelete } =
 *   useEntityActionDispatcher('items', { openModal });
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import {
    isAiConvertibleEntityType,
    isScrappableEntityType,
    normalizeActionEntityType,
} from "@/Entities/entity-actions-config";
import {
    getEntityRouteConfig,
    getEntitySingularRouteKey,
    resolveEntityRouteUrl,
} from "@/Composables/entity/entityRouteRegistry";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useEntityDofusdbRefresh } from "@/Composables/entity/useEntityDofusdbRefresh";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";
import { resolveEntityEditHref } from "@/Utils/entity/resolveEntityEditHref";

function getEntityId(entity) {
    return entity?.id ?? entity?._data?.id ?? null;
}

function getEntityLabel(entity) {
    return entity?.name || entity?.title || entity?._data?.name || entity?._data?.title || "cette entité";
}

export function useEntityActionDispatcher(entityType, handlers = {}) {
    const { copyToClipboard } = useCopyToClipboard();
    const { previewRefresh, applyRefresh } = useEntityDofusdbRefresh();
    const { isAdmin, canUpdateAny, authUser } = usePermissions();
    const notificationStore = useNotificationStore();

    const normalizedType = computed(() => normalizeActionEntityType(entityType?.value ?? entityType));
    const routeParamKey = computed(() => getEntitySingularRouteKey(normalizedType.value));

    const deleteConfirm = ref({
        open: false,
        title: "Placer en corbeille",
        message: "",
        details: [],
        entity: null,
        meta: {},
        loading: false,
    });

    function resetDeleteConfirm() {
        deleteConfirm.value = {
            open: false,
            title: "Placer en corbeille",
            message: "",
            details: [],
            entity: null,
            meta: {},
            loading: false,
        };
    }

    async function deleteEntityWithConfirmation(entity, meta = {}) {
        if (handlers.onDelete) {
            handlers.onDelete(entity, meta);
            return true;
        }

        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural) return false;

        let impact = null;
        try {
            const { data } = await axios.get(`/api/entities/${encodeURIComponent(plural)}/${entityId}/delete-impact`, {
                headers: { Accept: "application/json" },
            });
            impact = data;
        } catch {
            impact = null;
        }

        const relations = Array.isArray(impact?.relations) ? impact.relations : [];
        const mediaCount = Number(impact?.media_count ?? 0);
        const details = [
            relations.length > 0
                ? `${relations.length} relation(s) seront détachées en suppression définitive : ${relations.join(", ")}.`
                : null,
            mediaCount > 0
                ? `${mediaCount} média(s) lié(s) seront conservés en corbeille.`
                : null,
            "Un administrateur pourra restaurer l’entité depuis le journal admin.",
        ].filter(Boolean);

        deleteConfirm.value = {
            open: true,
            title: "Placer en corbeille",
            message: `Placer « ${getEntityLabel(entity)} » en corbeille ?`,
            details,
            entity,
            meta,
            loading: false,
        };

        return true;
    }

    async function confirmPendingDelete() {
        const pending = deleteConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural || pending.loading) return false;

        deleteConfirm.value = { ...pending, loading: true };
        try {
            await axios.delete(`/api/entities/${encodeURIComponent(plural)}/${entityId}`, {
                headers: { Accept: "application/json" },
            });
            handlers.onDeleted?.(entity, pending.meta);
            if (!handlers.onDeleted) {
                router.reload();
            }
            resetDeleteConfirm();
            return true;
        } catch (error) {
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible de placer l’entité en corbeille.";
            window.alert(message);
            deleteConfirm.value = { ...deleteConfirm.value, loading: false };
            return false;
        }
    }

    function cancelPendingDelete() {
        resetDeleteConfirm();
    }

    const refreshConfirm = ref({
        open: false,
        loading: false,
        applying: false,
        preview: null,
        error: "",
        playable: false,
        entity: null,
        entityLabel: "cette fiche",
        entityType: "",
        meta: {},
        showDofusdb: false,
        showAi: false,
        showJson: false,
        aiBrief: "",
        aiSubmitting: false,
        aiError: "",
        aiSuccess: "",
        aiEstimate: null,
        aiUsage: null,
        aiAction: "",
        aiActionLabel: "Conversion IA",
        diff: null,
        diffBusy: false,
    });

    function resetRefreshConfirm() {
        refreshConfirm.value = {
            open: false,
            loading: false,
            applying: false,
            preview: null,
            error: "",
            playable: false,
            entity: null,
            entityLabel: "cette fiche",
            entityType: "",
            meta: {},
            showDofusdb: false,
            showAi: false,
            showJson: false,
            aiBrief: "",
            aiSubmitting: false,
            aiError: "",
            aiSuccess: "",
            aiEstimate: null,
            aiUsage: null,
            aiAction: "",
            aiActionLabel: "Conversion IA",
            diff: null,
            diffBusy: false,
        };
    }

    function entityOwnerId(entity) {
        const raw = entity?._data ?? entity;
        const candidate = raw?.createdBy?.id ?? raw?.created_by?.id ?? raw?.created_by ?? null;
        const id = Number(candidate);
        return Number.isFinite(id) && id > 0 ? id : null;
    }

    function canUpdateEntity(entity, plural) {
        if (canUpdateAny(plural)) return true;
        const ownerId = entityOwnerId(entity);
        const userId = Number(authUser.value?.id ?? 0);
        return Boolean(userId && ownerId && userId === ownerId);
    }

    function actionForType(plural) {
        const map = {
            monsters: "encounter",
            spells: "spell",
            npcs: "npc",
            items: "item",
            consumables: "consumable",
        };
        return map[plural] || "";
    }

    function actionLabel(action) {
        const map = {
            encounter: "Rencontre (monstre + sorts)",
            spell: "Sort (effets)",
            npc: "PNJ (fiche complète)",
            item: "Équipement",
            consumable: "Consommable",
        };
        return map[action] || "Conversion IA";
    }

    function isPlayableEntity(entity) {
        const state = entity?.state || entity?.creature?.state || entity?._data?.state;
        return state === "playable" || state === "archived";
    }

    async function loadAiStatus(aiAction) {
        const action = aiAction || refreshConfirm.value.aiAction;
        try {
            const { data } = await axios.get("/api/ia/status", { headers: { Accept: "application/json" } });
            const estimates = Array.isArray(data?.estimates) ? data.estimates : [];
            const estimate = estimates.find((row) => row.action === action) || null;
            if (refreshConfirm.value.open) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiEstimate: estimate,
                    aiUsage: data?.usage && typeof data.usage === "object" ? data.usage : null,
                };
            }
        } catch (error) {
            if (!refreshConfirm.value.open) {
                return;
            }
            if (error?.response?.status === 423) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiUsage: null,
                };
                return;
            }
            refreshConfirm.value = {
                ...refreshConfirm.value,
                aiUsage: {
                    local_input_tokens: 0,
                    local_output_tokens: 0,
                    local_runs: 0,
                    remaining_credits_usd: null,
                },
            };
        }
    }

    async function openRefreshPanel(entity, meta = {}) {
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const showDofusdb = Boolean(entityId && isScrappableEntityType(plural) && canUpdateEntity(entity, plural));
        const aiAction = actionForType(plural);
        const showAi = Boolean(entityId && isAdmin.value && isAiConvertibleEntityType(plural) && aiAction);
        const showJson = Boolean(entityId && isAdmin.value);
        if (!entityId || (!showDofusdb && !showAi && !showJson)) return false;

        refreshConfirm.value = {
            open: true,
            loading: showDofusdb,
            applying: false,
            preview: null,
            error: "",
            playable: isPlayableEntity(entity),
            entity,
            entityLabel: getEntityLabel(entity),
            entityType: plural,
            meta,
            showDofusdb,
            showAi,
            showJson,
            aiBrief: "",
            aiSubmitting: false,
            aiError: "",
            aiSuccess: "",
            aiEstimate: null,
            aiUsage: null,
            aiAction,
            aiActionLabel: actionLabel(aiAction),
            diff: null,
            diffBusy: false,
        };

        if (showAi) {
            await loadAiStatus(aiAction);
        }

        if (!showDofusdb) {
            return true;
        }

        const preview = await previewRefresh(plural, entityId);
        if (!refreshConfirm.value.open) return false;
        if (!preview) {
            refreshConfirm.value = {
                ...refreshConfirm.value,
                loading: false,
                error: "Impossible de charger l’aperçu DofusDB.",
            };
            return false;
        }
        if (preview.success === false && preview.message && !preview.data) {
            refreshConfirm.value = {
                ...refreshConfirm.value,
                loading: false,
                error: String(preview.message),
            };
            return false;
        }
        refreshConfirm.value = {
            ...refreshConfirm.value,
            loading: false,
            preview,
            error: preview.success === false ? String(preview.message || "") : "",
        };
        return true;
    }

    function openDiffFromResponse(pending, data) {
        const diff = data?.diff && typeof data.diff === "object" ? data.diff : null;
        if (!diff) {
            const message = String(data?.message || "La mise à jour n’a renvoyé aucun comparatif.");
            refreshConfirm.value = {
                ...refreshConfirm.value,
                applying: false,
                aiSubmitting: false,
                error: message,
                aiError: message,
            };
            return false;
        }
        refreshConfirm.value = {
            ...pending,
            loading: false,
            applying: false,
            aiSubmitting: false,
            aiError: "",
            aiSuccess: "",
            diff,
            diffBusy: false,
            open: true,
        };
        return true;
    }

    async function submitAiConvert() {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const action = pending.aiAction || actionForType(plural);
        if (!entityId || !plural || pending.aiSubmitting || !action) return false;
        if (pending.aiUsage?.has_api_key === false) {
            refreshConfirm.value = {
                ...pending,
                aiError: "Clé Anthropic absente : aucun appel n’a été lancé.",
            };
            return false;
        }

        refreshConfirm.value = { ...pending, aiSubmitting: true, aiError: "", aiSuccess: "" };
        try {
            const { data } = await axios.post(
                `/api/entities/${encodeURIComponent(plural)}/${entityId}/ia-convert`,
                { action, brief: pending.aiBrief || null, force: Boolean(pending.playable) },
                { headers: { Accept: "application/json" }, timeout: 180000 },
            );
            if (data?.success === false || data?.queued === true) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiSubmitting: false,
                    aiError: data.message || "La conversion IA a échoué.",
                };
                return false;
            }
            return openDiffFromResponse(pending, data);
        } catch (error) {
            if (error?.response?.status === 423) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiSubmitting: false,
                    aiError: "Confirme ton mot de passe pour lancer une conversion IA.",
                };
                return false;
            }
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible de lancer la conversion IA.";
            refreshConfirm.value = { ...refreshConfirm.value, aiSubmitting: false, aiError: String(message) };
            return false;
        }
    }

    async function submitAiInject(options = {}) {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const action = pending.aiAction || actionForType(plural) || null;
        const payload = options?.payload;
        if (!entityId || !plural || pending.aiSubmitting) return false;
        if (!payload || typeof payload !== "object" || Array.isArray(payload)) {
            refreshConfirm.value = {
                ...pending,
                aiError: "Payload JSON invalide.",
            };
            return false;
        }

        refreshConfirm.value = { ...pending, aiSubmitting: true, aiError: "", aiSuccess: "" };
        try {
            const body = { payload, force: Boolean(pending.playable) };
            if (action) {
                body.action = action;
            }
            const { data } = await axios.post(
                `/api/entities/${encodeURIComponent(plural)}/${entityId}/ia-inject`,
                body,
                { headers: { Accept: "application/json" }, timeout: 60000 },
            );
            if (data?.success === false) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiSubmitting: false,
                    aiError: data.message || "L’injection JSON a échoué.",
                };
                return false;
            }
            return openDiffFromResponse(pending, data);
        } catch (error) {
            if (error?.response?.status === 423) {
                refreshConfirm.value = {
                    ...refreshConfirm.value,
                    aiSubmitting: false,
                    aiError: "Confirme ton mot de passe pour injecter un JSON.",
                };
                return false;
            }
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible d’injecter le JSON.";
            refreshConfirm.value = { ...refreshConfirm.value, aiSubmitting: false, aiError: String(message) };
            return false;
        }
    }

    async function confirmPendingRefresh(options = {}) {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        if (!entityId || !plural || pending.applying) return false;

        refreshConfirm.value = { ...pending, applying: true };
        const result = await applyRefresh(plural, entityId, {
            mode: options.mode === "images_only" ? "images_only" : "full",
            force: Boolean(options.force),
            includeImage: options.includeImage !== false,
        });
        if (!result?.success) {
            refreshConfirm.value = {
                ...refreshConfirm.value,
                applying: false,
                error: String(result?.body?.message || "Erreur lors de la mise à jour DofusDB."),
            };
            return false;
        }
        return openDiffFromResponse(pending, result.body);
    }

    function cancelPendingRefresh() {
        if (refreshConfirm.value.diff) {
            confirmUpdateDiffRestore();
            return;
        }
        resetRefreshConfirm();
    }

    async function confirmUpdateDiffSave(payload = {}) {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const snapshotId = pending.diff?.snapshot_id;
        const restoreKeys = Array.isArray(payload?.restore_keys) ? payload.restore_keys : [];
        if (!entityId || !plural || !snapshotId || pending.diffBusy) return false;

        refreshConfirm.value = { ...pending, diffBusy: true };
        try {
            const { data } = await axios.post(
                `/api/entities/${encodeURIComponent(plural)}/${entityId}/update-diff/apply`,
                { snapshot_id: snapshotId, restore_keys: restoreKeys },
                { headers: { Accept: "application/json" } },
            );
            notificationStore.success(data?.message || "Nouvelle version conservée.");
            handlers.onRefresh?.(entity, pending.meta);
            resetRefreshConfirm();
            return true;
        } catch (error) {
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible d’enregistrer le comparatif.";
            notificationStore.error(String(message));
            refreshConfirm.value = { ...refreshConfirm.value, diffBusy: false };
            return false;
        }
    }

    async function confirmUpdateDiffRestore() {
        const pending = refreshConfirm.value;
        const entity = pending.entity;
        const entityId = getEntityId(entity);
        const plural = normalizedType.value;
        const snapshotId = pending.diff?.snapshot_id;
        if (!entityId || !plural || !snapshotId || pending.diffBusy) return false;

        refreshConfirm.value = { ...pending, diffBusy: true };
        try {
            await axios.post(
                `/api/entities/${encodeURIComponent(plural)}/${entityId}/update-diff/restore`,
                { snapshot_id: snapshotId },
                { headers: { Accept: "application/json" } },
            );
            notificationStore.success("Version précédente rétablie.");
            handlers.onRefresh?.(entity, pending.meta);
            resetRefreshConfirm();
            return true;
        } catch (error) {
            const message =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || "Impossible de rétablir la version précédente.";
            notificationStore.error(String(message));
            refreshConfirm.value = { ...refreshConfirm.value, diffBusy: false };
            return false;
        }
    }

    async function dispatchEntityAction(actionKey, entity, meta = {}) {
        const entityId = getEntityId(entity);
        if (!entityId) return false;

        const plural = normalizedType.value;
        const paramKey = routeParamKey.value;
        const cfg = getEntityRouteConfig(paramKey);

        switch (actionKey) {
            case "view":
            case "expand":
            case "open-page":
                router.visit(route(`entities.${plural}.show`, { [paramKey]: entityId }));
                handlers.onOpenPage?.(entity, meta);
                return true;

            case "quick-view":
            case "open-modal": {
                if (typeof handlers.openModal === "function") {
                    handlers.openModal(entity, meta);
                    return true;
                }
                useEntitySurfaceStore().openView({
                    entityType: plural,
                    entityId,
                    entity,
                    tableMeta: meta?.tableMeta || {},
                });
                return true;
            }

            case "edit":
            case "edit-page":
            case "edit-modal": {
                const ctx =
                    meta?.context
                    || (typeof handlers.actionContext === "function" ? handlers.actionContext() : null)
                    || handlers.actionContext
                    || {};
                const surface = useEntitySurfaceStore();

                // Modal d’édition : « Agrandir » → page Modifier.
                if (ctx.inModal && ctx.modalMode === "edit") {
                    surface.expandTopEditToPage();
                    handlers.onEditPage?.(entity, meta);
                    return true;
                }

                // Modal d’affichage : remplacer par l’éditeur.
                if (ctx.inModal && ctx.modalMode === "view") {
                    if (typeof handlers.openEditModal === "function") {
                        handlers.openEditModal(entity, meta);
                    } else if (surface.top?.entityId === entityId && surface.top?.entityType === plural) {
                        surface.replaceTopWithEdit();
                    } else {
                        surface.openEdit({ entityType: plural, entityId, entity });
                    }
                    handlers.onEditPage?.(entity, meta);
                    return true;
                }

                // Carte minimale / ligne : éditeur en modal.
                if (
                    ctx.inMinimal
                    || ctx.viewMode === "minimal"
                    || ctx.inLine
                    || ctx.viewMode === "line"
                    || actionKey === "edit-modal"
                ) {
                    if (typeof handlers.openEditModal === "function") {
                        handlers.openEditModal(entity, meta);
                    } else {
                        surface.openEdit({ entityType: plural, entityId, entity });
                    }
                    handlers.onEditPage?.(entity, meta);
                    return true;
                }

                // Page fiche (ou défaut) : page Modifier.
                const editHref =
                    resolveEntityEditHref(plural, entityId)
                    || route(`entities.${plural}.edit`, { [paramKey]: entityId }, false);
                if (editHref) {
                    router.visit(editHref);
                }
                handlers.onEditPage?.(entity, meta);
                return true;
            }

            case "copy-link": {
                const url = resolveEntityRouteUrl(paramKey, "show", entityId, cfg);
                if (url) await copyToClipboard(url, "Lien de l'entité copié !");
                handlers.onCopyLink?.(entity, meta);
                return true;
            }

            case "refresh": {
                if (handlers.onRefreshRequest) {
                    await handlers.onRefreshRequest(entity, meta);
                } else {
                    await openRefreshPanel(entity, meta);
                }
                return true;
            }

            case "view-dofusdb": {
                // Ouvert via EntityActionButton / EntityActions → store Pinia.
                handlers.onViewDofusdb?.(entity, meta);
                return true;
            }

            case "delete":
                return deleteEntityWithConfirmation(entity, meta);

            default:
                handlers.onUnhandled?.(actionKey, entity, meta);
                return false;
        }
    }

    return {
        dispatchEntityAction,
        deleteConfirm,
        confirmPendingDelete,
        cancelPendingDelete,
        refreshConfirm,
        confirmPendingRefresh,
        cancelPendingRefresh,
        submitAiConvert,
        submitAiInject,
        reloadAiStatus: loadAiStatus,
        confirmUpdateDiffSave,
        confirmUpdateDiffRestore,
    };
}
