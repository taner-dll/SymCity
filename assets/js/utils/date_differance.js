

function minutesDiffNow(date) {
    return Math.round((((new Date()-date) % 86400000) % 3600000) / 60000);
}

export {minutesDiffNow};