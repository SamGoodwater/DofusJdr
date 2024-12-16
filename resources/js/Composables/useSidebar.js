import { ref, onMounted, onUnmounted } from "vue";

const isSidebarOpen = ref(true);

let waitBeforeToggle = false;

export function useSidebar() {
    const toggleSidebar = () => {
        if (!waitBeforeToggle) {
            isSidebarOpen.value = !isSidebarOpen.value;
            waitBeforeToggle = true;
            setTimeout(() => {
                waitBeforeToggle = false;
            }, 500);
        }
    };

    const handleKeydown = (event) => {
        if (event.altKey && event.key === "g") {
            toggleSidebar();
        }
    };

    onMounted(() => {
        window.addEventListener("keydown", handleKeydown);
    });

    onUnmounted(() => {
        window.removeEventListener("keydown", handleKeydown);
    });

    return {
        isSidebarOpen,
        toggleSidebar,
    };
}
