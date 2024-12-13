export function getSizeImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => {
            resolve({ w: img.width, h: img.height, url: url });
        };
        img.onerror = (error) => {
            reject(error);
        };
        img.src = url;
    });
}

export const imageExists = (url) => {
    const http = new XMLHttpRequest();
    http.open("HEAD", url, false);
    http.send();
    return http.status !== 404;
};
