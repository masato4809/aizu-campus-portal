import { ApolloError } from '@apollo/client';
import { useUsecasesCalendarUserScheduleListQuery } from '@/script/Graphql/codegen/graphql';
import {
  IUsecasesCalendarUserSchedule,
  parseUsecasesCalendarUserSchedulePayload,
  UseCasesCalendarUserScheduleList,
} from '@/script/Models/Usecases/UsecasesCalendarUserScheduleList';
import { DateTime } from '@/script/Common/DateTime';

export interface IAppCalendarUserScheduleList {
  emailList: string[];
  startDate: DateTime;
  endDate: DateTime;
}

export function useFetchAppCalendarUserScheduleList(
  fetchParameter: IAppCalendarUserScheduleList,
): [boolean, UseCasesCalendarUserScheduleList, ApolloError | undefined] {
  /**
   * クエリ実施.
   */
  const query = useUsecasesCalendarUserScheduleListQuery({
    variables: {
      emailList: fetchParameter.emailList,
      startDate: fetchParameter.startDate.toDateTime(),
      endDate: fetchParameter.endDate.toDateTime(),
    },
    fetchPolicy: 'no-cache',
  });

  // データのparse.
  const list = new UseCasesCalendarUserScheduleList(
    query.data?.UsecasesCalendarUserScheduleList.map(v =>
      parseUsecasesCalendarUserSchedulePayload(
        v as IUsecasesCalendarUserSchedule,
      ),
    ),
  );

  return [query.loading, list, query.error];
}
