export class Icons {
    static icons = {};

    static async loadIcons() {
        try {
            const response = await fetch("storage/icons/icons.json");
            if (!response.ok) {
                console.error("Failed to load icons");
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
