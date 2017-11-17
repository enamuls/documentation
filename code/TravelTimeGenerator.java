package ambulancetiming;

import java.io.BufferedReader;
import java.io.FileWriter;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.URL;
import java.net.URLConnection;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

/**
 *
 * @author Sumon M E H
 */
public class TravelTimeGenerator {

    private URL url;
    private URLConnection urlConnection;
    private BufferedReader br;
    private String inputLine, json;
    private StringBuilder sbuild;
    private static PrintWriter pw;

    public TravelTimeGenerator() {
        url = null;
        urlConnection = null;
        br = null;
        inputLine = "";
        json = "";
        sbuild = new StringBuilder();
    }

    public String generateTravelTime(String time, String jsonFileName, String urlToServer) throws Exception {
        try {
            url = new URL(urlToServer);
            urlConnection = url.openConnection();
            br = new BufferedReader(new InputStreamReader(urlConnection.getInputStream()));

            while ((inputLine = br.readLine()) != null) {
                //System.out.println(inputLine);
                sbuild.append(inputLine);
            }
            br.close();
            json = sbuild.toString();
            return parseJson(time, jsonFileName);
        } catch (Exception e) {
            e.printStackTrace();
            return "";
        }
    }

    public String parseJson(String time, String jsonFileName) throws Exception {
        pw = new PrintWriter(new FileWriter(jsonFileName, true));
        String output = "";
        try {
            JSONParser parser = new JSONParser();
            Object obj = parser.parse(json);
            JSONObject jb = (JSONObject) obj;

            //write to json file
            pw.println("{\"" + "time" + "\":[" + time + "]}" + jb.toJSONString());
            pw.flush();
            pw.close();

            //get the routes array
            JSONArray jsonArray1 = (JSONArray) jb.get("routes");

            for (int c = 0; c < jsonArray1.size(); c++) {
                JSONObject jsonObject1 = (JSONObject) jsonArray1.get(c);
                //get the legs array
                JSONArray jsonArray2 = (JSONArray) jsonObject1.get("legs");
                JSONObject jsonObject2 = (JSONObject) jsonArray2.get(0);
                //get the distance in traffic object
                JSONObject jsonObject3 = (JSONObject) jsonObject2.get("distance");
                String distance = (String) jsonObject3.get("text");
                String [] split = distance.split(" ");
                distance = split[0];
                //get the duration in traffic object
                JSONObject jsonObject4 = (JSONObject) jsonObject2.get("duration_in_traffic");
                //get the time duration string
                String duration = (String) jsonObject4.get("text");
                split = duration.split(" ");
                duration = split[0];

                output = output + duration + "," + distance + ",";
            }

            return output;
        } catch (Exception e) {
            return "error";
        }
    }
}
