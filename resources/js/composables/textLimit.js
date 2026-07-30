export function useTextLimit() {

    function textLimit(text, limit) {
        if(text?.length > limit) return text.substring(1, limit) + "..."
        else return text
    }

    return {
        textLimit
    }
}