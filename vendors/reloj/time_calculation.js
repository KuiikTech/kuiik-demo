const getMissingTime = (deadline) => {
  let now = new Date(),
    remainTime = (new Date(deadline) - now + 1000) / 1000,
    remainMilis = (
      "00" + Math.floor((new Date(deadline) - now + 1000) % 1000)
    ).slice(-3),
    remainSeconds = ("0" + Math.abs(Math.floor(remainTime % 60) + 1)).slice(-2),
    remainMinutes = (
      "0" + Math.abs(Math.floor((remainTime / 60) % 60) + 1)
    ).slice(-2),
    remainHours = (
      "0" + Math.abs(Math.floor((remainTime / 3600) % 24) + 1)
    ).slice(-2),
    remainDays = Math.abs(remainTime / (3600 * 24));

  return {
    remainSeconds,
    remainMinutes,
    remainHours,
    remainDays,
    remainTime,
    remainMilis,
  };
};

const getRemainingTime = (deadline) => {
  let now = new Date(),
    remainTime = (now - new Date(deadline) + 1000) / 1000,
    remainSeconds = ("0" + Math.abs(Math.floor(remainTime % 60) + 1)).slice(-2),
    remainMinutes = (
      "0" + Math.abs(Math.floor((remainTime / 60) % 60) + 1)
    ).slice(-2),
    remainHours = (
      "0" + Math.abs(Math.floor((remainTime / 3600) % 24) + 1)
    ).slice(-2),
    remainDays = Math.abs(remainTime / (3600 * 24) + 1);

  return {
    remainSeconds,
    remainMinutes,
    remainHours,
    remainDays,
    remainTime,
  };
};

const getRemainingTime_final = (deadline, deadline_final) => {
  let now = new Date(),
    remainTime = (new Date(deadline_final) - new Date(deadline) + 1000) / 1000,
    remainMilis = (
      "00" + Math.floor((now - new Date(deadline) + 1000) % 1000)
    ).slice(-3),
    remainSeconds = ("0" + Math.floor(remainTime % 60)).slice(-2),
    remainMinutes = ("0" + Math.floor((remainTime / 60) % 60)).slice(-2),
    remainHours = ("0" + Math.floor((remainTime / 3600) % 24)).slice(-2),
    remainDays = Math.floor(remainTime / (3600 * 24));

  return {
    remainSeconds,
    remainMinutes,
    remainHours,
    remainDays,
    remainTime,
    remainMilis,
  };
};

const countdown = (deadline, elem, finalMessage) => {
  const el = document.getElementById(elem);

  const timerUpdate = setInterval(() => {
    let t = getRemainingTime(deadline);
    //el.innerHTML = `${t.remainDays}d:${t.remainHours}h:${t.remainMinutes}m:${t.remainSeconds}s`;
    el.innerHTML = `<strong>${t.remainHours}h${t.remainMinutes}:${t.remainSeconds}</strong>`;

    if (t.remainTime >= 1) {
      clearInterval(timerUpdate);
      el.innerHTML = `<strong>${finalMessage}</strong>`;
    }
  }, 100);
};

const countup = (deadline, elem, finalMessage, alert, elem_alert) => {
  const el = document.getElementById(elem);

  const timerUpdate = setInterval(() => {
    let t = getMissingTime(deadline);
    //el.innerHTML = `${t.remainDays}d:${t.remainHours}h:${t.remainMinutes}m:${t.remainSeconds}s`;
    el.innerHTML = `<b>${t.remainHours}h${t.remainMinutes}:${t.remainSeconds}</b>`;

    if (t.remainTime >= 1) {
      clearInterval(timerUpdate);
      el.innerHTML = `<b>${finalMessage}</b>`;
    }
    var t_alert = alert * 60;
    if (alert != 0 && Math.abs(t.remainTime) > t_alert) {
      el.innerHTML = `<b>${t.remainHours}h${t.remainMinutes}:${t.remainSeconds} </b>`;
      if (elem_alert != null) {
        if (document.getElementById(elem_alert)) {
          const el_a = document.getElementById(elem_alert);
          el_a.style.display = "block";
        }
      }
    }
  }, 100);
};

const tiempo_final = (deadline, deadline_final, elem, finalMessage) => {
  const el = document.getElementById(elem);

  const timerUpdate = setInterval(() => {
    let t = getRemainingTime_final(deadline, deadline_final);
    //el.innerHTML = `${t.remainDays}d:${t.remainHours}h:${t.remainMinutes}m:${t.remainSeconds}s`;
    el.innerHTML = `<strong>${t.remainHours}h:${t.remainMinutes}m:${t.remainSeconds}s</strong>`;

    if (t.remainTime <= 1) {
      clearInterval(timerUpdate);
      el.innerHTML = `<strong>${finalMessage}</strong>`;
    }
  }, 100);
};
