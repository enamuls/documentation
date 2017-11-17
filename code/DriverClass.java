package ambulancetiming;

import java.io.File;
import java.io.FileWriter;
import java.io.PrintWriter;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

/**
 *
 * @author Sumon M E H
 */
public class DriverClass {

    private static DateTimeFormatter dtf = DateTimeFormatter.ofPattern("E,yyyy/MM/dd,HH:mm:ss");
    private static LocalDateTime now;
    private static TravelTimeGenerator ttg;
    private static int interval;
    private static String fileName, result, response;
    private static PrintWriter pw;

    public static void main(String[] args) throws Exception {
        fileName = "log.txt";
        File file = new File(fileName);
        interval = 5 * 60 * 1000; // every 5 minutes

        while (true) {

            try {
                if (!file.exists()) {
                    pw = new PrintWriter(new FileWriter(fileName));
                    pw.close();
                } 
                
                //request address1
                serverRequest("address1", "log1.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address1.php");

                //request address2
                serverRequest("address2", "log2.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address2.php");

                //request address3
                serverRequest("address3", "log3.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address3.php");

                //request address4
                serverRequest("address4", "log4.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address4.php");

                //request address5
                serverRequest("address5", "log5.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address5.php");

                //request address6
                serverRequest("address6", "log6.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address6.php");

                //request address7
                serverRequest("address7", "log7.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address7.php");

                //request address8
                serverRequest("address8", "log8.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address8.php");
                
                //request address9
                serverRequest("address9", "log9.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address9.php");

                //request address10
                serverRequest("address10", "log10.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address10.php");

                //request address11
                serverRequest("address11", "log11.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address11.php");

                //request address12
                serverRequest("address12", "log12.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address12.php");

                //request address13
                serverRequest("address13", "log13.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address13.php");

                //request address14
                serverRequest("address14", "log14.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address14.php");

                //request address15
                serverRequest("address15", "log15.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address15.php");

                //request address16
                serverRequest("address16", "log16.json", fileName, "http://operation3inc.com/wp-admin/CSE5ITP/Sumon/address16.php");

                System.out.println("\n------------------------------------------------------------------------\n");

            } catch (Exception e) {
                //System.out.println(e.toString());
            }
            
            Thread.sleep(interval);
        }
    }

    public static void serverRequest(String tableName, String jsonFileName, String fileName, String urlToServer) throws Exception {
        pw = new PrintWriter(new FileWriter(fileName, true));
        now = LocalDateTime.now();
        ttg = new TravelTimeGenerator();
        response = ttg.generateTravelTime("" + dtf.format(now), jsonFileName, urlToServer);
        result = tableName + "," + dtf.format(now) + "," + response;

        //if error then dont print
        if (!response.equalsIgnoreCase("error")) {
            System.out.println(result);
            pw.println(result);
        }
        pw.close();
        //uploadFiles(jsonFileName);
    }
    
}
