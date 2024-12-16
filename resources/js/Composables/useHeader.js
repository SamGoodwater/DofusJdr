import { ref, onMounted, onUnmounted } from "vue";

const isHeaderOpen = ref(true);
let waitBeforeToggle = false;

export function useHeader() {
    const toggleHeader = () => {
        if (!waitBeforeToggle) {
            isHeaderOpen.value = !isHeaderOpen.value;
            waitBeforeToggle = true;
            setTimeout(() => {
                waitBeforeToggle = false;
            }, 500);
        }
    };

    const handleKeydown = (event) => {
        if (event.altKey && event.key === "h") {
            toggleHeader();
        }
    };

    onMounted(() => {
        window.addEventListener("keydown", handleKeydown);
    });

    onUnmounted(() => {
        window.removeEventListener("keydown", handleKeydown);
    });

    return {
        isHeaderOpen,
        toggleHeader,
    };
}
