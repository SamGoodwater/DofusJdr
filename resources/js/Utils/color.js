// adjustement: number of intensity levels to adjust (1 to 9)
// direction: "auto" (default), "decrease" or "augmentation"
export function adjustIntensityColor(
    color,
    adjustment = 2,
    direction = "auto",
) {
    const intensities = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
    const colorParts = color.split("-");
    let baseColor, intensity;

    if (colorParts.length === 3) {
        baseColor = `${colorParts[0]}-${colorParts[1]}`;
        intensity = parseInt(colorParts[2]);
    } else if (colorParts.length === 2) {
        baseColor = colorParts[0];
        intensity = parseInt(colorParts[1]);
    } else {
        return color;
    }

    const currentIndex = intensities.indexOf(intensity);
    if (currentIndex === -1) {
        throw new Error("Invalid intensity value");
    }

    let newIndex;
    if (direction === "auto") {
        if (intensity >= 500) {
            newIndex = currentIndex - adjustment;
        } else {
            newIndex = currentIndex + adjustment;
        }
    } else if (direction === "decrease") {
        newIndex = currentIndex - adjustment;
    } else if (direction === "augmentation") {
        newIndex = currentIndex + adjustment;
    } else {
        throw new Error("Invalid direction value");
    }

    newIndex = Math.max(0, Math.min(intensities.length - 1, newIndex));

    return `${baseColor}-${intensities[newIndex]}`;
}
