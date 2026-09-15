import { ApolloError } from '@apollo/client';
import { useAppTrnDivisionListQuery } from '@/script/Graphql/codegen/graphql';
import {
  AppTrnDivisionList,
  IAppTrnDivision,
  parseAppTrnDivisionPayload,
} from '@/script/Models/App/Trn/TrnDivisionList';

export function useFetchAppTrnDivisionList(): [
  boolean,
  AppTrnDivisionList,
  ApolloError | undefined,
] {
  /**
   * クエリ実施.
   */
  const query = useAppTrnDivisionListQuery({});

  // データのparse.
  const list = new AppTrnDivisionList(
    query.data?.AppTrnDivisionList.map(v =>
      parseAppTrnDivisionPayload(v as IAppTrnDivision),
    ),
  );

  return [query.loading, list, query.error];
}
