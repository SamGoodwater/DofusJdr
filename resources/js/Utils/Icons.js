class Icons {
    static icons = {};

    static async loadIcons() {
        try {
            const response = await fetch("./icons.json");
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            Icons.icons = await response.json();
        } catch (error) {
            console.error(
                "There was a problem with the fetch operation:",
                error,
            );
        }
    }

    static async get(dir, name) {
        if (!Icons.iconsLoaded) {
            await Icons.loadIcons();
        }
        if (!Icons.icons[dir] || !Icons.icons[dir][name]) {
            return "";
        }
        return Icons.icons[dir][name];
    }
}

export default Icons;
