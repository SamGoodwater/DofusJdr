export class IconsGetter {
    static icons = {};
    static iconsLoaded = false;

    static async loadIcons() {
        try {
            const response = await fetch("storage/icons/icons.json");
            if (!response.ok) {
                console.error("Failed to load icons");
            }
            IconsGetter.icons = await response.json();
            IconsGetter.iconsLoaded = true;
        } catch (error) {
            console.error(
                "There was a problem with the fetch operation:",
                error,
            );
        }
    }

    static async get(dir, name) {
        if (!IconsGetter.iconsLoaded) {
            await IconsGetter.loadIcons();
        }
        if (!IconsGetter.icons[dir] || !IconsGetter.icons[dir][name]) {
            return "";
        }
        return IconsGetter.icons[dir][name];
    }
}
