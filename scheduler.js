import { exec } from "child_process";
import cron from "node-cron";

// Expresión cron: "0 0 1 1 *" => A las 00:00 del 1 de enero de cada año
// cron.schedule('0 0 1 1 *', () => {
//   console.log("📆 Ejecutando Laravel schedule (1 de enero a medianoche)...");

//   exec("php artisan usuarios:actualizar-antiguedad", (error, stdout, stderr) => {
//     if (error) {
//       console.error(`❌ Error al ejecutar: ${error.message}`);
//       return;
//     }
//     if (stderr) {
//       console.error(`⚠️ Stderr: ${stderr}`);
//       return;
//     }
//     console.log(`✅ Antiguedades actualizadas`);
//   });
// });


cron.schedule('* * * * *', () => {
  console.log("⏱ Ejecutando Laravel schedule:run cada minuto...");

  exec("php artisan usuarios:actualizar-antiguedad", (error, stdout, stderr) => {
    if (error) {
      console.error(`❌ Error al ejecutar: ${error.message}`);
      return;
    }
    if (stderr) {
      console.error(`⚠️ Stderr: ${stderr}`);
      return;
    }
    console.log(`✅ Output:\n${stdout}`);
  });
});
