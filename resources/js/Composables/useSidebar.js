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

    const checkIfSidebarIsOpen = () => {
        const sidebar = document.getElementById('menuSidebar');
        if(sidebar.classList.contains('sidebar-on')){
            isSidebarOpen.value = true;
            return true
        }
        isSidebarOpen.value = false;
        return false;
    }

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
        checkIfSidebarIsOpen
    };
}
